<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Review Sync settings live on the existing Integration Settings row.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('system_integration_settings')) {
            return;
        }

        Schema::table('system_integration_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('system_integration_settings', 'review_auto_sync_enabled')) {
                $table->boolean('review_auto_sync_enabled')->default(false);
            }
            if (!Schema::hasColumn('system_integration_settings', 'review_assignee_ids')) {
                $table->json('review_assignee_ids')->nullable();
            }
            if (!Schema::hasColumn('system_integration_settings', 'review_last_assigned_index')) {
                $table->integer('review_last_assigned_index')->default(-1);
            }
            if (!Schema::hasColumn('system_integration_settings', 'review_due_after_days')) {
                $table->unsignedSmallInteger('review_due_after_days')->default(3);
            }
            if (!Schema::hasColumn('system_integration_settings', 'review_links')) {
                $table->json('review_links')->nullable();
            }
            if (!Schema::hasColumn('system_integration_settings', 'review_whatsapp_template')) {
                $table->text('review_whatsapp_template')->nullable();
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('system_integration_settings')) {
            return;
        }
        Schema::table('system_integration_settings', function (Blueprint $table) {
            foreach ([
                'review_auto_sync_enabled',
                'review_assignee_ids',
                'review_last_assigned_index',
                'review_due_after_days',
                'review_links',
                'review_whatsapp_template',
            ] as $col) {
                if (Schema::hasColumn('system_integration_settings', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
