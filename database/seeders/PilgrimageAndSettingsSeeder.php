<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\SystemIntegrationSetting;
use App\Models\PilgrimageSetting;
use App\User;

class PilgrimageAndSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Define Pilgrimage & Integration Settings Permissions
        $permissions = [
            'view pilgrimage',
            'create pilgrimage',
            'edit pilgrimage',
            'delete pilgrimage',
            'view integration settings',
            'edit integration settings',
        ];

        foreach ($permissions as $permName) {
            Permission::firstOrCreate(
                ['name' => $permName],
                ['guard_name' => 'web']
            );
        }

        // 2. Assign to Admin Roles if they exist
        $adminRoles = Role::whereIn('name', ['admin', 'Admin', 'Super Admin', 'super-admin', 'manager'])->get();
        foreach ($adminRoles as $role) {
            $role->givePermissionTo($permissions);
        }

        // If no admin role exists yet, create 'admin' and give all permissions
        if ($adminRoles->isEmpty()) {
            $adminRole = Role::firstOrCreate(['name' => 'admin'], ['guard_name' => 'web']);
            $adminRole->givePermissionTo($permissions);
        }

        // Also assign to the primary user / admin user if exists
        $adminUser = User::first();
        if ($adminUser && method_exists($adminUser, 'givePermissionTo')) {
            try {
                $adminUser->givePermissionTo($permissions);
            } catch (\Throwable $e) {
                // Ignore if already assigned or not configured
            }
        }

        // 3. Initialize System Integration & Round-Robin Settings
        $systemSettings = SystemIntegrationSetting::getSettings();

        // 4. Default Tour & Pilgrimage Settings
        if (class_exists(PilgrimageSetting::class)) {
            PilgrimageSetting::firstOrCreate(
                ['id' => 1],
                [
                    'makkah_ops_contact' => '+966 50 123 4567',
                    'madinah_ops_contact' => '+966 50 765 4321',
                    'jeddah_ops_contact' => '+966 50 999 8888',
                    'general_hotline' => '+92 300 1234567',
                    'ziyaraat_offset_hours' => 48,
                    'reminder_window_hours' => 48,
                    'reminder_repeat_hours' => 3,
                ]
            );
        }

        $this->command->info('Pilgrimage permissions, System Integration & Lead Auto-Assignment settings seeded successfully.');
    }
}
