<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function users()
    {
        $users = User::orderByRaw("FIELD(role, 'administrador', 'coordenador', 'participante')")
            ->orderBy('name')
            ->paginate(50);

        return view('admin.users', compact('users'));
    }

    public function updateRole(Request $request, User $user)
    {
        $validated = $request->validate([
            'role' => ['required', 'in:participante,coordenador,administrador'],
        ]);

        // Não pode rebaixar a si mesmo
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Você não pode alterar seu próprio papel.');
        }

        $user->update(['role' => $validated['role']]);

        return back()->with('success', "Papel de '{$user->name}' alterado para {$validated['role']}.");
    }
}
