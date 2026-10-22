<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CreateAdmin extends Command
{
    protected $signature = 'app:create-admin {--name=} {--email=} {--password=}';

    protected $description = 'Create (or reset the password of) an administrator account';

    public function handle(): int
    {
        $name = $this->option('name') ?: $this->ask('Name', 'Administrator');
        $email = $this->option('email') ?: $this->ask('Email address');
        $password = $this->option('password') ?: $this->secret('Password (min 8 characters)');

        if (! filter_var($email, FILTER_VALIDATE_EMAIL) || strlen((string) $password) < 8) {
            $this->error('A valid email and a password of at least 8 characters are required.');

            return self::FAILURE;
        }

        $user = User::updateOrCreate(['email' => strtolower($email)], ['name' => $name, 'password' => $password, 'role' => 'admin', 'is_active' => true]);
        $this->info(($user->wasRecentlyCreated ? 'Created' : 'Updated').' administrator '.$user->email);

        return self::SUCCESS;
    }
}
