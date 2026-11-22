<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Membuat / memperbarui akun admin dari .env (tidak ada kata sandi yang ditulis di kode):
 *     ADMIN_EMAIL=admin@byuss.test
 *     ADMIN_PASSWORD=isi-kata-sandi-kuat
 * Jalankan: php artisan db:seed --class=AdminSeeder
 */
class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('ADMIN_EMAIL');
        $password = env('ADMIN_PASSWORD');

        if (! $email || ! $password) {
            $this->command?->error('Isi ADMIN_EMAIL dan ADMIN_PASSWORD di file .env dulu.');
            return;
        }

        $user = User::firstOrNew(['email' => $email]);
        $user->forceFill([
            'name'     => $user->name ?: 'Admin Byuss',
            'password' => Hash::make($password),
            'is_admin' => true,
        ])->save();

        $this->command?->info("Akun admin siap: {$email}");
    }
}
