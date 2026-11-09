<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class StrictlyIsolateSuperAdminPermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Purge any rogue assignment of 'Super-Admin' permission to non-superadmin roles (such as Admin or Manager)
        $perm = DB::table('permissions')->where('name', 'Super-Admin')->first();
        if ($perm) {
            DB::table('role_has_permissions')
                ->where('permission_id', $perm->id)
                ->where('role_id', '!=', 1)
                ->delete();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // No reverse needed as non-superadmins should never have super-admin privileges
    }
}
