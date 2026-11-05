<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'phone')) {
                    $table->string('phone', 50)->nullable()->after('email');
                }
                if (!Schema::hasColumn('users', 'location')) {
                    $table->string('location', 191)->nullable()->after('phone');
                }
                if (!Schema::hasColumn('users', 'about_me')) {
                    $table->text('about_me')->nullable()->after('location');
                }
            });

            try {
                DB::statement("ALTER TABLE `users` MODIFY COLUMN `role_id` INT(11) NOT NULL DEFAULT 2");
            } catch (\Throwable $e) {
                // Ignore if column type differs slightly
            }
        }

        if (Schema::hasTable('roles')) {
            try {
                DB::statement("ALTER TABLE `roles` MODIFY COLUMN `guard_name` VARCHAR(191) NOT NULL DEFAULT 'web'");
            } catch (\Throwable $e) {
            }
        }

        if (Schema::hasTable('permissions')) {
            try {
                DB::statement("ALTER TABLE `permissions` MODIFY COLUMN `guard_name` VARCHAR(191) NOT NULL DEFAULT 'web'");
            } catch (\Throwable $e) {
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                $cols = [];
                foreach (['phone', 'location', 'about_me'] as $col) {
                    if (Schema::hasColumn('users', $col)) {
                        $cols[] = $col;
                    }
                }
                if (!empty($cols)) {
                    $table->dropColumn($cols);
                }
            });
        }
    }
};
