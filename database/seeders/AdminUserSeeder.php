<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = 'admin@horusgroup.com';
        $admin = User::where('email', $email)->first();

        // Preserve the existing administrator and password when moving from the old store.
        if (!$admin) {
            $legacyAdmin = User::where('email', 'admin@altawheed.com')->first();
            if ($legacyAdmin && $legacyAdmin->hasRole('super_admin')) {
                $legacyAdmin->update(['email' => $email, 'name' => 'مدير مؤسسة حورس']);
                $admin = $legacyAdmin->fresh();
            }
        }

        if (!$admin) {
            $password = env('ADMIN_INITIAL_PASSWORD');
            if (!$password) {
                throw new RuntimeException('Set ADMIN_INITIAL_PASSWORD before creating the Horus administrator account.');
            }

            $admin = User::create([
                'name' => 'مدير مؤسسة حورس',
                'email' => $email,
                'password' => Hash::make($password),
                'phone' => '01002527072',
                'address' => '٢٢ع حدائق الأهرام، بوابة حورس',
                'is_active' => true,
                'email_verified_at' => now(),
            ]);
        } else {
            $admin->update([
                'name' => 'مدير مؤسسة حورس',
                'phone' => '01002527072',
                'address' => '٢٢ع حدائق الأهرام، بوابة حورس',
                'is_active' => true,
            ]);
        }

        $admin->syncRoles(['super_admin']);
    }
}
