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
        // 1. Pilgrimage / Dynamic Itinerary Bookings
        if (!Schema::hasTable('pilgrimage_bookings')) {
            Schema::create('pilgrimage_bookings', function (Blueprint $table) {
                $table->id();
                $table->string('crm_id', 50)->unique();
                $table->string('service_type', 50)->default('umrah'); // umrah, hajj, tour, corporate, custom
                $table->unsignedBigInteger('submitting_agent_id');
                $table->string('leader_name', 191);
                $table->string('leader_passport_no', 50)->nullable();
                $table->string('leader_contact_no', 50)->nullable();
                $table->unsignedInteger('total_persons')->default(1);
                $table->text('notes')->nullable();
                $table->enum('overall_status', ['active', 'completed', 'cancelled'])->default('active');
                $table->dateTime('submitted_at');
                $table->timestamps();

                $table->index('crm_id');
                $table->index('submitting_agent_id');
                $table->index('overall_status');
            });
        }

        // 2. Pilgrims / Co-Travelers
        if (!Schema::hasTable('booking_pilgrims')) {
            Schema::create('booking_pilgrims', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('booking_id');
                $table->string('name', 191);
                $table->string('passport_no', 50)->nullable();
                $table->string('gender', 20)->nullable();
                $table->string('age_type', 20)->default('adult'); // adult, child, infant
                $table->timestamps();

                $table->foreign('booking_id')->references('id')->on('pilgrimage_bookings')->onDelete('cascade');
            });
        }

        // 3. Flight Segments
        if (!Schema::hasTable('booking_flights')) {
            Schema::create('booking_flights', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('booking_id');
                $table->string('flight_no', 50);
                $table->string('airline', 100)->nullable();
                $table->string('from_city', 100);
                $table->string('to_city', 100);
                $table->dateTime('etd');
                $table->dateTime('eta')->nullable();
                $table->string('pnr', 50)->nullable();
                $table->timestamps();

                $table->foreign('booking_id')->references('id')->on('pilgrimage_bookings')->onDelete('cascade');
            });
        }

        // 4. Hotel Accommodations
        if (!Schema::hasTable('booking_hotels')) {
            Schema::create('booking_hotels', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('booking_id');
                $table->string('city', 100);
                $table->string('hotel_name', 191);
                $table->string('voucher_no', 100)->nullable();
                $table->unsignedInteger('room_qty')->default(1);
                $table->string('room_type', 50)->default('Double'); // Double, Triple, Quad, Quint, Hexa, Penta, Suite, Sharing
                $table->dateTime('check_in');
                $table->dateTime('check_out');
                $table->boolean('schedule_ziyaraat')->default(true);
                $table->timestamps();

                $table->foreign('booking_id')->references('id')->on('pilgrimage_bookings')->onDelete('cascade');
            });
        }

        // 5. Trackable Itinerary Legs
        if (!Schema::hasTable('itinerary_legs')) {
            Schema::create('itinerary_legs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('booking_id');
                $table->string('leg_type', 50); // flight, hotel_checkin, hotel_checkout, ziyaraat, custom_transfer, custom_activity
                $table->string('title', 191);
                $table->string('city_or_route', 191);
                $table->dateTime('event_datetime');
                $table->text('details')->nullable();
                $table->enum('status', ['pending', 'done'])->default('pending');
                $table->dateTime('completed_at')->nullable();
                $table->unsignedBigInteger('completed_by')->nullable();
                $table->dateTime('last_notified_at')->nullable();
                $table->unsignedInteger('notification_count')->default(0);
                $table->string('source_type', 100)->nullable();
                $table->unsignedBigInteger('source_id')->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();

                $table->foreign('booking_id')->references('id')->on('pilgrimage_bookings')->onDelete('cascade');
                $table->index(['event_datetime', 'status']);
                $table->index(['booking_id', 'status']);
            });
        }

        // 6. Pilgrimage & Operations Settings
        if (!Schema::hasTable('pilgrimage_settings')) {
            Schema::create('pilgrimage_settings', function (Blueprint $table) {
                $table->id();
                $table->string('makkah_ops_contact', 100)->default('+966 50 123 4567');
                $table->string('madinah_ops_contact', 100)->default('+966 50 765 4321');
                $table->string('jeddah_ops_contact', 100)->default('+966 50 999 8888');
                $table->string('general_hotline', 100)->default('+92 300 1234567');
                $table->unsignedInteger('ziyaraat_offset_hours')->default(48);
                $table->unsignedInteger('reminder_window_hours')->default(48);
                $table->unsignedInteger('reminder_repeat_hours')->default(3);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('itinerary_legs');
        Schema::dropIfExists('booking_hotels');
        Schema::dropIfExists('booking_flights');
        Schema::dropIfExists('booking_pilgrims');
        Schema::dropIfExists('pilgrimage_bookings');
        Schema::dropIfExists('pilgrimage_settings');
    }
};
