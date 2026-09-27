<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Reset Cached Roles/Permissions (Wajib agar tidak error cache)
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Buat Role 'Superadmin' jika belum ada
        $role = Role::firstOrCreate(
            ['name' => 'Superadmin', 'guard_name' => 'web']
        );

        // 3. Buat User Superadmin
        // Credentials come from .env (never from the repository). Without
        // ADMIN_PASSWORD a random one is generated and printed once.
        $email = env('ADMIN_EMAIL', 'superadmin@gmail.com');
        $password = env('ADMIN_PASSWORD') ?: \Illuminate\Support\Str::password(16);

        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name'              => env('ADMIN_NAME', 'Super Administrator'),
                'username'          => 'superadmin', // Pastikan username diisi
                'password'          => Hash::make($password),
                'no_wa'             => '08123456789',
                'email_verified_at' => now(),
                'is_active'         => true,
            ]
        );

        // 4. Assign Role Superadmin ke User tersebut
        if (!$user->hasRole('Superadmin')) {
            $user->assignRole($role);
        }

        if (! $user->wasRecentlyCreated) {
            $this->command->info("User Superadmin sudah ada ({$email}) — password tidak diubah.");

            return;
        }

        $this->command->info('User Superadmin berhasil dibuat!');
        $this->command->info("Email: {$email}");
        $this->command->info(env('ADMIN_PASSWORD') ? 'Pass : (sesuai ADMIN_PASSWORD di .env)' : "Pass : {$password}  <- simpan sekarang, tidak ditampilkan lagi");
    }
}
