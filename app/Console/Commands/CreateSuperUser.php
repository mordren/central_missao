<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateSuperUser extends Command
{
    protected $signature = 'user:superadmin
                            {--email= : Email do usuário existente para promover}
                            {--phone= : Telefone opcional para criar novo superusuário}
                            {--name= : Nome para criar novo superusuário}
                            {--password= : Senha para novo superusuário}';

    protected $description = 'Cria ou promove um usuário para administrador';

    public function handle(): int
    {
        $email = $this->option('email');

        if ($email) {
            $user = User::where('email', $email)->first();

            if (!$user) {
                $this->error("Usuário com email {$email} não encontrado.");
                return self::FAILURE;
            }

            $user->update(['role' => 'administrador']);
            $this->info("Usuário '{$user->name}' promovido a administrador.");
            return self::SUCCESS;
        }

        $name = $this->option('name') ?? $this->ask('Nome do superusuário');
        $email = $this->option('email') ?? $this->ask('Email do superusuário');
        $phone = $this->option('phone') ?? $this->ask('Telefone (opcional)');
        $password = $this->option('password') ?? $this->secret('Senha');

        if (User::where('email', $email)->exists()) {
            $this->error("Já existe um usuário com esse email.");
            return self::FAILURE;
        }

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'password' => Hash::make($password),
            'role' => 'administrador',
            'city' => 'Admin',
            'neighborhood' => 'Admin',
            'referral_code' => md5($email . now()),
            'points' => 0,
        ]);

        $this->info("Superusuário '{$user->name}' criado com sucesso!");
        return self::SUCCESS;
    }
}
