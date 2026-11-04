<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('organization_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('organization_settings', 'logo_height')) {
                $table->integer('logo_height')->default(55)->nullable();
            }
            if (!Schema::hasColumn('organization_settings', 'logo_width')) {
                $table->integer('logo_width')->default(180)->nullable();
            }
            if (!Schema::hasColumn('organization_settings', 'logo_padding')) {
                $table->integer('logo_padding')->default(4)->nullable();
            }
            if (!Schema::hasColumn('organization_settings', 'logo_margin_top')) {
                $table->integer('logo_margin_top')->default(0)->nullable();
            }
            if (!Schema::hasColumn('organization_settings', 'logo_margin_bottom')) {
                $table->integer('logo_margin_bottom')->default(0)->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organization_settings', function (Blueprint $table) {
            $table->dropColumn([
                'logo_height',
                'logo_width',
                'logo_padding',
                'logo_margin_top',
                'logo_margin_bottom',
            ]);
        });
    }
};
