<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class CreateCommercial extends Command
{
    protected $signature = 'app:create-commercial {email?} {--name=Commercial}';

    protected $description = 'Créer explicitement un commercial, sans mot de passe prédéfini';

    public function handle(): int
    {
        $email = $this->argument('email') ?: $this->ask('Adresse e-mail du commercial');
        $name = $this->option('name');
        $password = $this->secret('Mot de passe (12 caractères minimum)');
        $confirmation = $this->secret('Confirmer le mot de passe');

        $validator = Validator::make([
            'email' => $email,
            'name' => $name,
            'password' => $password,
            'password_confirmation' => $confirmation,
        ], [
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'name' => ['required', 'string', 'max:100'],
            'password' => ['required', 'string', 'min:12', 'confirmed'],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        // Ne jamais promouvoir ou écraser un compte existant.
        $user = new User([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'role' => 'commercial',
        ]);
        $user->email_verified_at = now();
        $user->save();

        $this->info('Compte commercial créé. Transmettre ses accès séparément du code.');

        return self::SUCCESS;
    }
}
