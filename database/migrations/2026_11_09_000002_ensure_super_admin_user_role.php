<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class EnsureSuperAdminUserRole extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Ensure user ID 1 is assigned the Super-Admin role (role_id 1)
        DB::table('users')->where('id', 1)->update([
            'role_id' => 1,
            'name' => 'Super Admin',
        ]);

        // Synchronize Spatie model_has_roles for user ID 1
        $exists = DB::table('model_has_roles')->where('model_id', 1)->first();
        if ($exists) {
            DB::table('model_has_roles')->where('model_id', 1)->update(['role_id' => 1]);
        } else {
            DB::table('model_has_roles')->insert([
                'role_id' => 1,
                'model_type' => 'App\\Models\\User',
                'model_id' => 1,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
