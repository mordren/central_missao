<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ChangePasswordController extends Controller
{
    public function show()
    {
        return view('auth.change-password');
    }

    public function update(Request $request)
    {
        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password.required' => 'A senha é obrigatória.',
            'password.min' => 'A senha deve ter pelo menos 8 caracteres.',
            'password.confirmed' => 'Confirmação de senha incorreta.',
        ]);

        $user = $request->user();
        $user->password = $request->password;
        $user->force_password_change = false;
        $user->save();

        $request->session()->regenerate();

        return redirect()->intended('/dashboard')->with('success', 'Senha atualizada com sucesso.');
    }
}
