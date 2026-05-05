<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivityPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class ActivityPhotoController extends Controller
{
    /**
     * Upload photos to a completed mission.
     * All authenticated users can upload.
     */
    public function store(Request $request, Activity $activity)
    {

        $request->validate([
            'photos'           => ['required', 'array', 'min:1', 'max:10'],
            'photos.*'         => ['required', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'captions'         => ['nullable', 'array'],
            'captions.*'       => ['nullable', 'string', 'max:255'],
        ], [
            'photos.required'  => 'Selecione pelo menos uma foto.',
            'photos.*.image'   => 'Apenas arquivos de imagem são aceitos.',
            'photos.*.mimes'   => 'Formatos aceitos: JPG, PNG, WebP.',
            'photos.*.max'     => 'Cada foto deve ter no máximo 5 MB.',
        ]);

        $captions = $request->input('captions', []);
        $count = 0;

        $destination = public_path('images/activity-photos');
        if (!is_dir($destination)) {
            mkdir($destination, 0755, true);
        }

        foreach ($request->file('photos') as $index => $file) {
            // Generate a safe filename — never trust the original name
            $extension = strtolower($file->getClientOriginalExtension());
            $safeName = 'activity_' . $activity->id . '_' . time() . '_' . Str::random(10) . '.' . $extension;

            $file->move($destination, $safeName);
            $path = 'public/images/activity-photos/' . $safeName;

            ActivityPhoto::create([
                'activity_id' => $activity->id,
                'user_id'     => Auth::id(),
                'path'        => $path,
                'caption'     => isset($captions[$index]) ? strip_tags(trim($captions[$index])) : null,
                'status'      => 'pending',
            ]);

            $count++;
        }

        return back()->with('success', $count . ' foto(s) enviada(s) com sucesso. Aguardando aprovação.');
    }

    /**
     * Approve a photo (admin and coordinator only).
     */
    public function approve(Request $request, ActivityPhoto $photo)
    {
        Gate::authorize('moderate', $photo);

        $photo->update([
            'status'      => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'rejected_by' => null,
            'rejected_at' => null,
        ]);

        return back()->with('success', 'Foto aprovada.');
    }

    /**
     * Reject a photo (admin and coordinator only).
     * Rejected photos are hidden but kept; a nightly/manual cleanup can purge them.
     */
    public function reject(Request $request, ActivityPhoto $photo)
    {
        Gate::authorize('moderate', $photo);

        $photo->update([
            'status'      => 'rejected',
            'rejected_by' => Auth::id(),
            'rejected_at' => now(),
            'approved_by' => null,
            'approved_at' => null,
        ]);

        return back()->with('success', 'Foto recusada.');
    }

    /**
     * Delete a photo (owner can delete, admin/coordinator can delete any).
     */
    public function destroy(Activity $activity, ActivityPhoto $photo)
    {
        Gate::authorize('delete', $photo);

        // File deletion handled by model booted() observer
        $photo->delete();

        return back()->with('success', 'Foto removida.');
    }
}
