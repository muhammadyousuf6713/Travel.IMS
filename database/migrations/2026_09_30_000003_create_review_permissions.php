<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Creates the Reviews permissions (grouped under a "Reviews" module) and grants them
 * to every role / user that already holds "Super-Admin", so admins get access right away.
 *
 *  Reviews list  - open the Reviews page (sees only reviews assigned to them)
 *  Reviews all   - see every review + reassign
 *  Reviews sync  - "Sync to Reviews" button on the Inquiry list
 */
return new class extends Migration
{
    private array $children = ['Reviews list', 'Reviews all', 'Reviews sync'];

    public function up(): void
    {
        if (!Schema::hasTable('permissions')) {
            return;
        }

        $hasParent = Schema::hasColumn('permissions', 'parent_id');
        $now = now();

        $parentId = 0;
        if ($hasParent) {
            $parentId = DB::table('permissions')->where('name', 'Reviews')->where('guard_name', 'web')->value('id');
            if (!$parentId) {
                $parentId = DB::table('permissions')->insertGetId([
                    'name' => 'Reviews', 'guard_name' => 'web', 'parent_id' => 0,
                    'created_at' => $now, 'updated_at' => $now,
                ]);
            }
        }

        $newIds = [];
        foreach ($this->children as $name) {
            $id = DB::table('permissions')->where('name', $name)->where('guard_name', 'web')->value('id');
            if (!$id) {
                $row = ['name' => $name, 'guard_name' => 'web', 'created_at' => $now, 'updated_at' => $now];
                if ($hasParent) {
                    $row['parent_id'] = $parentId;
                }
                $id = DB::table('permissions')->insertGetId($row);
            }
            $newIds[] = $id;
        }

        // Grant to holders of Super-Admin (roles and direct user permissions)
        $superId = DB::table('permissions')->where('name', 'Super-Admin')->value('id');
        if ($superId) {
            if (Schema::hasTable('role_has_permissions')) {
                $roleIds = DB::table('role_has_permissions')->where('permission_id', $superId)->pluck('role_id');
                foreach ($roleIds as $roleId) {
                    foreach ($newIds as $pid) {
                        DB::table('role_has_permissions')->insertOrIgnore(['permission_id' => $pid, 'role_id' => $roleId]);
                    }
                }
            }
            if (Schema::hasTable('model_has_permissions')) {
                $models = DB::table('model_has_permissions')->where('permission_id', $superId)->get(['model_type', 'model_id']);
                foreach ($models as $m) {
                    foreach ($newIds as $pid) {
                        DB::table('model_has_permissions')->insertOrIgnore([
                            'permission_id' => $pid, 'model_type' => $m->model_type, 'model_id' => $m->model_id,
                        ]);
                    }
                }
            }
        }

        $this->flushPermissionCache();
    }

    public function down(): void
    {
        if (!Schema::hasTable('permissions')) {
            return;
        }
        $ids = DB::table('permissions')->whereIn('name', array_merge($this->children, ['Reviews']))->pluck('id');
        if (Schema::hasTable('role_has_permissions')) {
            DB::table('role_has_permissions')->whereIn('permission_id', $ids)->delete();
        }
        if (Schema::hasTable('model_has_permissions')) {
            DB::table('model_has_permissions')->whereIn('permission_id', $ids)->delete();
        }
        DB::table('permissions')->whereIn('id', $ids)->delete();
        $this->flushPermissionCache();
    }

    private function flushPermissionCache(): void
    {
        try {
            app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        } catch (\Throwable $e) {
            // cache flush is best-effort
        }
    }
};
