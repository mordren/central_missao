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
            'name' => ['required', 'string', 'max:255', 'regex:/^[^\d]+$/u'],
            // Require DDD (2 digits) + 9-digit mobile (starts with 9)
                'phone' => ['required', 'string', 'regex:/^\(?([1-9][0-9])\)?\s?9\d{4}-?\d{4}$/'],
            'email' => ['nullable', 'email', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'neighborhood' => ['required', 'string', 'max:255'],
            'referral_code_input' => ['nullable', 'string', 'max:50'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required' => 'O nome é obrigatório.',
            'name.regex' => 'O nome não pode conter números.',
            'phone.required' => 'O telefone é obrigatório.',
            'phone.regex' => 'Formato inválido. Use DDD + 9 dígitos: (11) 99999-9999',
            'city.required' => 'A cidade é obrigatória.',
            'neighborhood.required' => 'O bairro é obrigatório.',
            'password.required' => 'A senha é obrigatória.',
            'password.min' => 'A senha deve ter no mínimo 8 caracteres.',
            'password.confirmed' => 'As senhas não conferem.',
        ]);

            // Normalize phone to digits only (e.g. (11) 99999-9999 => 11999999999)
            $normalizedPhone = preg_replace('/\D+/', '', $validated['phone']);

            // Enforce DDD (2 digits) + 9-digit mobile (starts with 9)
            if (!preg_match('/^[1-9][0-9]9[0-9]{8}$/', $normalizedPhone)) {
                return back()->withErrors(['phone' => 'Formato inválido. Use DDD + 9 dígitos: (11) 99999-9999'])->withInput();
            }

            // Check uniqueness against normalized phone stored in DB
            if (User::where('phone', $normalizedPhone)->exists()) {
                return back()->withErrors(['phone' => 'Este telefone já está cadastrado.'])->withInput();
            }

        $user = User::create([
            'name' => $validated['name'],
                'phone' => $normalizedPhone,
            'email' => $validated['email'] ?? null,
            'city' => $validated['city'],
            'neighborhood' => $validated['neighborhood'],
            'referred_by' => $validated['referral_code_input'] ?? null,
                'referral_code' => strtoupper(substr(md5($normalizedPhone . time()), 0, 8)),
            'password' => $validated['password'],
        ]);

        Auth::login($user);

        return redirect('/dashboard');
    }
}
