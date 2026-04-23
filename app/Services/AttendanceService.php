<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AttendanceService
{
    /**
     * RSVP: user pre-confirms they intend to attend.
     * Only permitted for users with role 'standard'.
     * Idempotent — calling twice has no extra effect.
     *
     * @return 'confirmed'|'already_confirmed'|'unauthorized'
     */
    public function confirmRsvp(User $user, Activity $activity): string
    {
        if ($user->role !== 'participante') {
            return 'unauthorized';
        }

        $pivot = DB::table('activity_user')
            ->where('activity_id', $activity->id)
            ->where('user_id', $user->id)
            ->first();

        if ($pivot && $pivot->rsvp_confirmed) {
            return 'already_confirmed';
        }

        if ($pivot) {
            DB::table('activity_user')
                ->where('activity_id', $activity->id)
                ->where('user_id', $user->id)
                ->update([
                    'rsvp_confirmed' => true,
                    'updated_at'     => now(),
                ]);
        } else {
            DB::table('activity_user')->insert([
                'activity_id'    => $activity->id,
                'user_id'        => $user->id,
                'status'         => 'pendente',
                'rsvp_confirmed' => true,
                'did_participate' => false,
                'penalty_applied' => false,
                'confirmed_at'   => null,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
        }

        return 'confirmed';
    }

    /**
     * Cancel RSVP — removes the pre-confirmation.
     * Only allowed if the user has not yet participated.
     *
     * @return 'cancelled'|'not_confirmed'|'already_participated'|'unauthorized'
     */
    public function cancelRsvp(User $user, Activity $activity): string
    {
        if ($user->role !== 'participante') {
            return 'unauthorized';
        }

        $pivot = DB::table('activity_user')
            ->where('activity_id', $activity->id)
            ->where('user_id', $user->id)
            ->first();

        if (! $pivot || ! $pivot->rsvp_confirmed) {
            return 'not_confirmed';
        }

        if ($pivot->did_participate) {
            return 'already_participated';
        }

        // If the row only exists because of the RSVP (never participated), delete it.
        // Otherwise just unset the flag.
        if ($pivot->status === 'pendente') {
            DB::table('activity_user')
                ->where('activity_id', $activity->id)
                ->where('user_id', $user->id)
                ->delete();
        } else {
            DB::table('activity_user')
                ->where('activity_id', $activity->id)
                ->where('user_id', $user->id)
                ->update([
                    'rsvp_confirmed' => false,
                    'updated_at'     => now(),
                ]);
        }

        return 'cancelled';
    }

    /**
     * @return 'double_points'|'points_awarded'|'already_participated'
     */
    public function recordParticipation(User $user, Activity $activity): string
    {
        $pivot = DB::table('activity_user')
            ->where('activity_id', $activity->id)
            ->where('user_id', $user->id)
            ->first();

        if ($pivot && $pivot->did_participate) {
            return 'already_participated';
        }

        $rsvpConfirmed  = $pivot && $pivot->rsvp_confirmed;
        $multiplier     = $rsvpConfirmed ? 2 : 1;
        $pointsAwarded  = $activity->points * $multiplier;

        DB::transaction(function () use ($user, $activity, $pivot, $pointsAwarded) {
            if ($pivot) {
                DB::table('activity_user')
                    ->where('activity_id', $activity->id)
                    ->where('user_id', $user->id)
                    ->update([
                        'status'          => 'confirmado',
                        'did_participate' => true,
                        'points_awarded'  => $pointsAwarded,
                        'penalty_applied' => false,
                        'confirmed_at'    => now(),
                        'updated_at'      => now(),
                    ]);
            } else {
                DB::table('activity_user')->insert([
                    'activity_id'     => $activity->id,
                    'user_id'         => $user->id,
                    'status'          => 'confirmado',
                    'rsvp_confirmed'  => false,
                    'did_participate' => true,
                    'points_awarded'  => $pointsAwarded,
                    'penalty_applied' => false,
                    'confirmed_at'    => now(),
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);
            }

            DB::table('users')
                ->where('id', $user->id)
                ->increment('points', (int) $pointsAwarded);
        });

        return $rsvpConfirmed ? 'double_points' : 'points_awarded';
    }

    /**
     * Lazy penalty processing — call on activity index load or activity creation.
     * Finds activities older than 24 h where a user RSVPed but never participated.
     * Deducts 50 % of the mission's base points from the user (floor at 0).
     * Fully idempotent: runs only when penalty_applied = false.
     */
    public function processLazyPenalties(): void
    {
        $cutoff = Carbon::now()->subHours(24);

        $rows = DB::table('activity_user as au')
            ->join('activities as a', 'a.id', '=', 'au.activity_id')
            ->where('a.date_time', '<=', $cutoff)
            ->where('au.rsvp_confirmed', true)
            ->where('au.did_participate', false)
            ->where('au.penalty_applied', false)
            ->select(
                'au.activity_id',
                'au.user_id',
                'a.points as base_points'
            )
            ->get();

        foreach ($rows as $row) {
            $penalty = (int) ceil($row->base_points * 0.5);

            DB::transaction(function () use ($row, $penalty) {
                // Re-check inside the transaction with a row lock to prevent concurrency issues.
                $pivot = DB::table('activity_user')
                    ->where('activity_id', $row->activity_id)
                    ->where('user_id', $row->user_id)
                    ->where('penalty_applied', false)
                    ->lockForUpdate()
                    ->first();

                if (! $pivot) {
                    return; // Already handled by a concurrent request
                }

                // Read current points within the same transaction.
                $user = DB::table('users')
                    ->where('id', $row->user_id)
                    ->lockForUpdate()
                    ->first();

                $newPoints = max(0, $user->points - $penalty);

                DB::table('activity_user')
                    ->where('activity_id', $row->activity_id)
                    ->where('user_id', $row->user_id)
                    ->update([
                        'penalty_applied' => true,
                        'updated_at'      => now(),
                    ]);

                DB::table('users')
                    ->where('id', $row->user_id)
                    ->update(['points' => $newPoints]);
            });
        }
    }
}
