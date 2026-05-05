<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;

class AlbumController extends Controller
{
    /**
     * Listing of all completed missions that have at least one approved photo.
     */
    public function index()
    {
        $missions = Activity::where(function ($q) {
                $q->whereNotNull('deadline')->where('deadline', '<', now())
                  ->orWhere(function ($q2) {
                      $q2->whereNull('deadline')->where('date_time', '<', now());
                  });
            })
            ->whereHas('approvedPhotos')
            ->withCount(['photos as approved_photos_count' => function ($q) {
                $q->where('status', 'approved');
            }])
            ->orderByDesc('date_time')
            ->get();

        return view('albums.index', compact('missions'));
    }

    /**
     * Full photo album for a single completed mission.
     */
    public function show(Activity $activity)
    {
        if (!$activity->isExpired()) {
            abort(404);
        }

        $approvedPhotos = $activity->approvedPhotos()->with('uploader')->get();

        // Pending photos visible only to their uploader or moderators
        $pendingPhotos = collect();
        if (auth()->check()) {
            $user = auth()->user();
            if ($user->isAdmin() || $user->isCoordinator()) {
                $pendingPhotos = $activity->photos()
                    ->where('status', 'pending')
                    ->with('uploader')
                    ->orderBy('created_at')
                    ->get();
            } else {
                $pendingPhotos = $activity->photos()
                    ->where('status', 'pending')
                    ->where('user_id', auth()->id())
                    ->with('uploader')
                    ->orderBy('created_at')
                    ->get();
            }
        }

        return view('activities.album', compact('activity', 'approvedPhotos', 'pendingPhotos'));
    }
}
