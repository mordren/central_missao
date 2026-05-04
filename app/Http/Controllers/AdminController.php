<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function leads(Request $request)
    {
        $query = User::query()->select([
            'id', 'name', 'phone', 'email', 'city', 'neighborhood', 'referred_by',
            'date_of_birth', 'religion', 'education_level', 'higher_course',
            'profession', 'how_known', 'first_spokesperson',
            'pauta1', 'pauta2', 'pauta3', 'political_ambition', 'current_status',
            'created_at',
        ])->orderBy('name');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name',         'like', "%{$search}%")
                  ->orWhere('email',        'like', "%{$search}%")
                  ->orWhere('phone',        'like', "%{$search}%")
                  ->orWhere('city',         'like', "%{$search}%")
                  ->orWhere('neighborhood', 'like', "%{$search}%");
            });
        }

        $leads = $query->paginate(50)->withQueryString();

        return view('admin.leads', compact('leads', 'search'));
    }
    public function users()
    {
        $users = User::orderByRaw("
            CASE
                WHEN role = 'administrador' THEN 1
                WHEN role = 'coordenador' THEN 2
                WHEN role = 'participante' THEN 3
                ELSE 4
            END
        ")
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
