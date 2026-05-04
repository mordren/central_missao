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
            'date_of_birth'            => 'required|date',
            'religion'                 => 'required|string|max:120',
            'religion_outro'           => 'nullable|string|max:120',
            'education_level'          => 'required|string|max:120',
            'higher_course'            => 'nullable|string|max:150',
            'profession'               => 'nullable|string|max:150',
            'how_known'                => 'required|string|max:1000',
            'first_spokesperson'       => 'required|string|max:150',
            'first_spokesperson_outro' => 'nullable|string|max:150',
            'pauta1'                   => 'required|string|max:250',
            'pauta2'                   => 'nullable|string|max:250',
            'pauta3'                   => 'nullable|string|max:250',
            'political_ambition'       => 'required|string|max:150',
            'current_status'           => 'required|string|max:255',
        ]);

        // When "Outra" is selected, use the typed custom value instead
        if (($data['religion'] ?? '') === 'Outra' && !empty($data['religion_outro'])) {
            $data['religion'] = $data['religion_outro'];
        }
        unset($data['religion_outro']);

        if (($data['first_spokesperson'] ?? '') === 'Outro' && !empty($data['first_spokesperson_outro'])) {
            $data['first_spokesperson'] = $data['first_spokesperson_outro'];
        }
        unset($data['first_spokesperson_outro']);

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
