<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpandedProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        return view('profile.complete', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'date_of_birth' => 'nullable|date',
            'religion' => 'nullable|string|max:120',
            'education_level' => 'nullable|string|max:120',
            'higher_course' => 'nullable|string|max:150',
            'profession' => 'nullable|string|max:150',
            'how_known' => 'nullable|string|max:1000',
            'first_spokesperson' => 'nullable|string|max:150',
            'pauta1' => 'nullable|string|max:250',
            'pauta2' => 'nullable|string|max:250',
            'pauta3' => 'nullable|string|max:250',
            'political_ambition' => 'nullable|string|max:150',
            'current_status' => 'nullable|string|max:150',
        ]);

        $wasCompleted = (bool) $user->profile_completed_at;

        $user->fill($data);

        if (! $wasCompleted) {
            $user->points = ($user->points ?? 0) + 15;
            $user->profile_completed_at = now();
        }

        $user->save();

        return redirect()->route('dashboard')->with('success', 'Cadastro atualizado. +15 pontos');
    }
}
