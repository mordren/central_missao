<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'name'       => 'required|string|max:150',
            'phone'      => 'required|string|max:20',
            'city'       => 'nullable|string|max:100',
            'neighborhood' => 'nullable|string|max:100',
            'nickname'   => [
                'nullable',
                'string',
                'min:3',
                'max:30',
                'regex:/^[\p{L}\p{N} _]+$/u',
                Rule::unique('users', 'nickname')->ignore($user->id),
            ],
            'avatar_file' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
            'avatar_url'  => [
                'nullable',
                'string',
                'url',
                'max:500',
            ],
        ], [
            'nickname.regex' => 'O apelido só pode conter letras (incluindo acentos), números, espaços e underscores.',
            'nickname.unique' => 'Este apelido já está em uso.',
            'avatar_file.mimes' => 'O avatar deve ser JPG, PNG ou WebP.',
            'avatar_file.max' => 'O avatar não pode ter mais de 2 MB.',
            'avatar_url.url' => 'A URL do avatar não é válida.',
        ]);

        // Handle avatar upload
        if ($request->hasFile('avatar_file') && $request->file('avatar_file')->isValid()) {
            // Delete old uploaded avatar if exists
            if ($user->avatar_path) {
                Storage::disk('public')->delete($user->avatar_path);
            }
            $path = $request->file('avatar_file')->store('avatars', 'public');
            $user->avatar_path = $path;
            $user->avatar_url  = null; // clear external URL when uploading a file
        } elseif (!empty($data['avatar_url'])) {
            // Using external URL — clear any stored file
            if ($user->avatar_path) {
                Storage::disk('public')->delete($user->avatar_path);
                $user->avatar_path = null;
            }
            $user->avatar_url = $data['avatar_url'];
        }

        $user->name         = $data['name'];
        $user->phone        = $data['phone'];
        $user->city         = $data['city'] ?? null;
        $user->neighborhood = $data['neighborhood'] ?? null;
        $user->nickname     = $data['nickname'] ?? null;

        $user->save();

        return redirect()->route('profile.show')->with('success', 'Perfil atualizado com sucesso.');
    }
}
