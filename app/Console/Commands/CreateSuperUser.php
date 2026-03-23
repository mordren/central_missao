<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateSuperUser extends Command
{
    protected $signature = 'user:superadmin
                            {--phone= : Telefone do usuário existente para promover}
                            {--name= : Nome para criar novo superusuário}
                            {--password= : Senha para novo superusuário}';

    protected $description = 'Cria ou promove um usuário para administrador';

    public function handle(): int
    {
        $phone = $this->option('phone');

        if ($phone) {
            $user = User::where('phone', $phone)->first();

            if (!$user) {
                $this->error("Usuário com telefone {$phone} não encontrado.");
                return self::FAILURE;
            }

            $user->update(['role' => 'administrador']);
            $this->info("Usuário '{$user->name}' promovido a administrador.");
            return self::SUCCESS;
        }

        $name = $this->option('name') ?? $this->ask('Nome do superusuário');
        $phone = $this->ask('Telefone (ex: (11) 99999-9999)');
        $password = $this->option('password') ?? $this->secret('Senha');

        if (User::where('phone', $phone)->exists()) {
            $this->error("Já existe um usuário com esse telefone.");
            return self::FAILURE;
        }

        $user = User::create([
            'name' => $name,
            'phone' => $phone,
            'password' => Hash::make($password),
            'role' => 'administrador',
            'city' => 'Admin',
            'neighborhood' => 'Admin',
            'referral_code' => md5($phone . now()),
            'points' => 0,
        ]);

        $this->info("Superusuário '{$user->name}' criado com sucesso!");
        return self::SUCCESS;
    }
}
