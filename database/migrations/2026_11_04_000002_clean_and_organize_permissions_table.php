<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('permissions')) {
            return;
        }

        $now = now();

        // 1. Remove obsolete Author Dashboard permissions
        $obsoleteNames = ['Author Dashboard', 'Author Dashboard list', 'Author Dashboard view'];
        $obsoleteIds = DB::table('permissions')->whereIn('name', $obsoleteNames)->pluck('id');
        if ($obsoleteIds->isNotEmpty()) {
            if (Schema::hasTable('role_has_permissions')) {
                DB::table('role_has_permissions')->whereIn('permission_id', $obsoleteIds)->delete();
            }
            if (Schema::hasTable('model_has_permissions')) {
                DB::table('model_has_permissions')->whereIn('permission_id', $obsoleteIds)->delete();
            }
            DB::table('permissions')->whereIn('id', $obsoleteIds)->delete();
        }

        // Helper to find or create permission
        $findOrCreate = function ($name, $parentId = 0) use ($now) {
            $record = DB::table('permissions')->where('name', $name)->where('guard_name', 'web')->first();
            if ($record) {
                if ($record->parent_id != $parentId && $parentId !== null) {
                    DB::table('permissions')->where('id', $record->id)->update(['parent_id' => $parentId, 'updated_at' => $now]);
                }
                return $record->id;
            }
            return DB::table('permissions')->insertGetId([
                'name' => $name,
                'guard_name' => 'web',
                'parent_id' => $parentId ?? 0,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        };

        // 2. Identify / create parent modules
        $preferencesId = $findOrCreate('Preferences', 0);
        $operationsId = $findOrCreate('Operations', 0);
        $adminId = $findOrCreate('Administrator', 0);
        $reportsId = $findOrCreate('Reports', 0);

        // 3. Fix Inquiry Types hierarchy under Preferences
        $inquiryTypesId = $findOrCreate('Inquiry Types', $preferencesId);
        $inquiryTypeActions = [
            'Inquiry Type list',
            'Inquiry Type add',
            'Inquiry Type edit',
            'Inquiry Type view',
            'Inquiry Type delete',
        ];
        foreach ($inquiryTypeActions as $act) {
            DB::table('permissions')->where('name', $act)->where('guard_name', 'web')->update([
                'parent_id' => $inquiryTypesId,
                'updated_at' => $now,
            ]);
        }

        // 4. Fix Reports permission
        $findOrCreate('view reports', $reportsId);

        // 5. Add / Organize Currencies permissions under Preferences
        $currenciesModuleId = $findOrCreate('Currencies', $preferencesId);
        $currencyListId = $findOrCreate('Currencies list', $currenciesModuleId);
        $currencyEditId = $findOrCreate('Currencies edit', $currenciesModuleId);

        // 6. Add / Organize Organization Settings under Administrator
        $orgModuleId = $findOrCreate('Organization Settings', $adminId);
        $orgEditId = $findOrCreate('Organization Settings edit', $orgModuleId);

        // 7. Add / Organize Pilgrimage & Itinerary under Operations
        $pilgrimageId = $findOrCreate('Pilgrimage & Itinerary', $operationsId);
        $pilgrimListId = $findOrCreate('Pilgrimage list', $pilgrimageId);
        $pilgrimCreateId = $findOrCreate('Pilgrimage create', $pilgrimageId);
        $pilgrimEditId = $findOrCreate('Pilgrimage edit', $pilgrimageId);
        $pilgrimDeleteId = $findOrCreate('Pilgrimage delete', $pilgrimageId);

        // 8. Make sure Sidebar permission exists
        $sidebarId = $findOrCreate('Sidebar', 0);

        // 9. Collect all admin-essential permission IDs to auto-grant to administrative roles
        $adminPermIds = [
            $currencyListId,
            $currencyEditId,
            $orgEditId,
            $pilgrimListId,
            $pilgrimCreateId,
            $pilgrimEditId,
            $pilgrimDeleteId,
            $sidebarId,
        ];

        // Ensure every existing role has 'Sidebar' so nobody's layout breaks
        if (Schema::hasTable('roles') && Schema::hasTable('role_has_permissions')) {
            $allRoleIds = DB::table('roles')->pluck('id');
            foreach ($allRoleIds as $rId) {
                DB::table('role_has_permissions')->insertOrIgnore([
                    'permission_id' => $sidebarId,
                    'role_id' => $rId,
                ]);
            }

            // Find administrative roles
            $adminRoles = DB::table('roles')
                ->whereIn(DB::raw('LOWER(name)'), ['super admin', 'admin', 'administrator', 'super-admin'])
                ->pluck('id');

            // Also roles that have Super-Admin permission
            $superAdminPermId = DB::table('permissions')->where('name', 'Super-Admin')->value('id');
            if ($superAdminPermId) {
                $superRoleIds = DB::table('role_has_permissions')
                    ->where('permission_id', $superAdminPermId)
                    ->pluck('role_id');
                $adminRoles = $adminRoles->merge($superRoleIds)->unique();
            }

            foreach ($adminRoles as $roleId) {
                foreach ($adminPermIds as $pId) {
                    DB::table('role_has_permissions')->insertOrIgnore([
                        'permission_id' => $pId,
                        'role_id' => $roleId,
                    ]);
                }
            }
        }

        // 10. Flush Spatie cache
        try {
            app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        } catch (\Throwable $e) {
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Safe no-op to preserve permissions
    }
};
