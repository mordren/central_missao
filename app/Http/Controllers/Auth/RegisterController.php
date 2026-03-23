<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'unique:users,phone', 'regex:/^\(?[1-9]{2}\)?\s?9?\d{4}-?\d{4}$/'],
            'email' => ['nullable', 'email', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'neighborhood' => ['required', 'string', 'max:255'],
            'referral_code_input' => ['nullable', 'string', 'max:50'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required' => 'O nome é obrigatório.',
            'phone.required' => 'O telefone é obrigatório.',
            'phone.unique' => 'Este telefone já está cadastrado.',
            'phone.regex' => 'Formato de telefone inválido. Ex: (11) 99999-9999',
            'city.required' => 'A cidade é obrigatória.',
            'neighborhood.required' => 'O bairro é obrigatório.',
            'password.required' => 'A senha é obrigatória.',
            'password.min' => 'A senha deve ter no mínimo 8 caracteres.',
            'password.confirmed' => 'As senhas não conferem.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'city' => $validated['city'],
            'neighborhood' => $validated['neighborhood'],
            'referred_by' => $validated['referral_code_input'] ?? null,
            'referral_code' => strtoupper(substr(md5($validated['phone'] . time()), 0, 8)),
            'password' => $validated['password'],
        ]);

        Auth::login($user);

        return redirect('/dashboard');
    }
}
