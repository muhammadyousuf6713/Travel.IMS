<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Ensure Inquiry module parent permission exists (id 15 or by name)
        $inquiryParent = Permission::where('name', 'Inquiry')->first();
        $parentId = $inquiryParent ? $inquiryParent->id : 15;

        // 2. Define new granular inquiry action permissions
        $newPermissions = [
            'Inquiry follow-up' => $parentId,
            'Inquiry assign' => $parentId,
            'Inquiry bulk assign' => $parentId,
        ];

        foreach ($newPermissions as $name => $pId) {
            $perm = Permission::firstOrCreate(
                ['name' => $name, 'guard_name' => 'web'],
                ['parent_id' => $pId]
            );
            if ($perm && $perm->parent_id != $pId) {
                $perm->update(['parent_id' => $pId]);
            }
        }

        // 3. Ensure Dashboard permission has parent_id 0
        $dashPerm = Permission::where('name', 'Dashboard')->first();
        if ($dashPerm && $dashPerm->parent_id !== 0) {
            $dashPerm->update(['parent_id' => 0]);
        }

        // 4. Automatically grant new permissions to Super-Admin and Admin roles initially
        $roles = Role::whereIn('name', ['Super-Admin', 'Super Admin', 'Admin', 'admin'])->get();
        foreach ($roles as $role) {
            foreach (array_keys($newPermissions) as $permName) {
                if (!$role->hasPermissionTo($permName)) {
                    $role->givePermissionTo($permName);
                }
            }
            if ($dashPerm && !$role->hasPermissionTo('Dashboard')) {
                $role->givePermissionTo('Dashboard');
            }
        }

        // 5. Clean up direct user-level permission overrides so non-SuperAdmin users strictly inherit from their assigned Roles
        $usersWithRoles = \App\Models\User::whereNotNull('role_id')->where('role_id', '!=', 1)->get();
        foreach ($usersWithRoles as $user) {
            $user->syncPermissions([]);
        }

        // Clear Spatie cache
        try {
            app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        } catch (\Throwable $e) {}
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Permission::whereIn('name', [
            'Inquiry follow-up',
            'Inquiry assign',
            'Inquiry bulk assign'
        ])->delete();

        try {
            app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        } catch (\Throwable $e) {}
    }
};
