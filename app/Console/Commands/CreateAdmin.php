<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

/**
 * Buat atau perbarui akun admin:
 *     php artisan admin:create
 *     php artisan admin:create admin@contoh.com
 * Kata sandi ditanyakan lewat terminal (tidak tersimpan di file mana pun).
 */
class CreateAdmin extends Command
{
    protected $signature = 'admin:create {email? : Email admin} {--name=Admin Byuss : Nama tampilan}';

    protected $description = 'Buat atau perbarui akun admin Byuss Cakery';

    public function handle(): int
    {
        $email = $this->argument('email') ?: $this->ask('Email admin');
        $password = $this->secret('Kata sandi (minimal 8 karakter)');

        $validator = Validator::make(
            ['email' => $email, 'password' => $password],
            ['email' => ['required', 'email'], 'password' => ['required', 'min:8']]
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $message) {
                $this->error($message);
            }
            return self::FAILURE;
        }

        $user = User::firstOrNew(['email' => $email]);
        $user->forceFill([
            'name'     => $user->name ?: $this->option('name'),
            'password' => Hash::make($password),
            'is_admin' => true,
        ])->save();

        $this->info("Akun admin siap: {$email}");

        return self::SUCCESS;
    }
}
