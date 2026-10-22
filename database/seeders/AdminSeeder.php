<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Creates (or resets) the first administrator from ADMIN_EMAIL / ADMIN_PASSWORD / ADMIN_NAME
 * in .env, so a fresh install can be seeded without an interactive console.
 */
class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = strtolower(trim((string) config('fortress.admin.email')));
        $password = (string) config('fortress.admin.password');

        if ($email === '' || $password === '') {
            $this->command?->warn('AdminSeeder: ADMIN_EMAIL / ADMIN_PASSWORD not set – skipped. Use `php artisan app:create-admin` instead.');

            return;
        }
        if (! filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 8) {
            $this->command?->error('AdminSeeder: ADMIN_EMAIL must be a valid address and ADMIN_PASSWORD at least 8 characters – skipped.');

            return;
        }

        $user = User::updateOrCreate(
            ['email' => $email],
            ['name' => config('fortress.admin.name') ?: 'Administrator', 'password' => $password, 'role' => 'admin', 'is_active' => true]
        );
        $this->command?->info('AdminSeeder: '.($user->wasRecentlyCreated ? 'created' : 'updated').' administrator '.$email.'. Remove ADMIN_PASSWORD from .env once signed in.');
    }
}
