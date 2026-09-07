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
        if (!Schema::hasTable('system_integration_settings')) {
            Schema::create('system_integration_settings', function (Blueprint $table) {
                $table->id();
                $table->boolean('auto_assign_enabled')->default(true);
                $table->json('assigned_user_ids')->nullable(); // Array of user IDs in round-robin order
                $table->integer('last_assigned_user_index')->default(-1);
                $table->unsignedBigInteger('default_sales_reference_id')->nullable(); // Defaults to Facebook/Meta
                $table->unsignedBigInteger('default_inquiry_type_id')->nullable();
                $table->string('default_inquiry_status', 50)->default('Open');
                $table->string('api_key', 64)->unique();
                $table->string('meta_verify_token', 64)->default('travel_ims_meta_webhook_token');
                $table->text('meta_page_access_token')->nullable();
                $table->string('meta_app_id', 100)->nullable();
                $table->string('meta_app_secret', 100)->nullable();
                $table->boolean('public_form_enabled')->default(true);
                $table->string('public_form_title', 191)->default('Travel Inquiry & Booking Request');
                $table->string('public_form_subtitle', 255)->default('Submit your travel requirements and our specialist will get back to you shortly.');
                $table->unsignedBigInteger('total_leads_processed')->default(0);
                $table->dateTime('last_lead_at')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_integration_settings');
    }
};
