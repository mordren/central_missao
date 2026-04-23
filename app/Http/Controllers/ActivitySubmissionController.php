<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivitySubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ActivitySubmissionController extends Controller
{
    public function store(Request $request, Activity $activity)
    {
        $user = $request->user();

        // only allow manual-task activities
        if ($activity->type !== 'tarefa_manual') {
            return redirect()->route('activities.show', $activity)->with('error', 'Esta atividade não aceita envio de arquivos.');
        }

        if ($activity->isExpired()) {
            return redirect()->route('activities.show', $activity)->with('error', 'O prazo para envio desta atividade já encerrou.');
        }

        $validated = $request->validate([
            'submission_file' => ['required', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,doc,docx,txt'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        $file = $validated['submission_file'];
        $dir = 'activities/' . $activity->id;
        $filename = time() . '_' . preg_replace('/[^A-Za-z0-9.\-_]/', '_', $file->getClientOriginalName());
        $path = $file->storeAs($dir, $filename, 'public');

        // Check if user already has a non-approved submission — replace it
        $existing = ActivitySubmission::where('activity_id', $activity->id)
            ->where('user_id', $user->id)
            ->whereIn('status', ['pending', 'rejected'])
            ->latest()
            ->first();

        if ($existing) {
            // Delete old file
            if (Storage::disk('public')->exists($existing->file_path)) {
                Storage::disk('public')->delete($existing->file_path);
            }
            $existing->update([
                'file_path'        => $path,
                'original_name'    => $file->getClientOriginalName(),
                'mime_type'        => $file->getClientMimeType(),
                'file_size'        => $file->getSize(),
                'status'           => 'pending',
                'comment'          => $request->input('comment'),
                'submitted_at'     => now(),
                'reviewed_by'      => null,
                'reviewed_at'      => null,
                'reviewer_comment' => null,
                'points_awarded'   => 0,
            ]);
            return redirect()->route('activities.show', $activity)->with('success', 'Arquivo substituído com sucesso. Aguardando nova revisão.');
        }

        ActivitySubmission::create([
            'activity_id'   => $activity->id,
            'user_id'       => $user->id,
            'file_path'     => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type'     => $file->getClientMimeType(),
            'file_size'     => $file->getSize(),
            'status'        => 'pending',
            'comment'       => $request->input('comment'),
            'submitted_at'  => now(),
        ]);

        return redirect()->route('activities.show', $activity)->with('success', 'Arquivo enviado com sucesso. Aguardando revisão do coordenador.');
    }

    // Admin/Coordinator: list submissions
    public function index(Request $request)
    {
        $query = ActivitySubmission::with(['activity', 'user', 'reviewer'])->orderByDesc('submitted_at');
        if ($request->has('status') && in_array($request->get('status'), ['pending', 'approved', 'rejected'])) {
            $query->where('status', $request->get('status'));
        }

        $submissions = $query->paginate(20)->withQueryString();

        return view('admin.activity_submissions.index', compact('submissions'));
    }

    // Admin/Coordinator: show single submission
    public function show(ActivitySubmission $submission)
    {
        $submission->load(['activity', 'user', 'reviewer']);
        return view('admin.activity_submissions.show', compact('submission'));
    }

    // Approve a submission: attach participant and award points
    public function approve(Request $request, ActivitySubmission $submission)
    {
        $activity = $submission->activity;
        $user = $submission->user;

        $request->validate([
            'points_to_award' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ]);

        $pointsToAward = (int) $request->input('points_to_award', $activity->points);

        DB::transaction(function () use ($submission, $activity, $user, $pointsToAward) {
            // mark pivot as confirmed (attach or update)
            $existing = $activity->participants()->where('user_id', $user->id)->first();
            if (!$existing) {
                $activity->participants()->attach($user->id, ['status' => 'confirmado', 'confirmed_at' => now()]);
            } else {
                $activity->participants()->updateExistingPivot($user->id, ['status' => 'confirmado', 'confirmed_at' => now()]);
            }

            // award points - only if not already awarded
            if (($submission->points_awarded === null || $submission->points_awarded == 0) && $pointsToAward > 0) {
                $user->increment('points', $pointsToAward);
                $submission->points_awarded = $pointsToAward;
            }

            $submission->status = 'approved';
            $submission->reviewed_by = auth()->id();
            $submission->reviewed_at = now();
            $submission->save();
        });

        return redirect()->route('admin.activity_submissions.index')->with('success', 'Submissão aprovada com sucesso.');
    }

    // Reject a submission
    public function reject(Request $request, ActivitySubmission $submission)
    {
        $submission->status = 'rejected';
        $submission->reviewed_by = auth()->id();
        $submission->reviewed_at = now();
        $submission->reviewer_comment = $request->input('reviewer_comment');
        $submission->save();

        return redirect()->route('admin.activity_submissions.index')->with('success', 'Submissão recusada.');
    }
}
