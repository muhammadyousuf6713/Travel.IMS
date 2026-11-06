<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('hotels')) {
            Schema::create('hotels', function (Blueprint $table) {
                $table->id();
                $table->string('name', 191);
                $table->string('city', 100);
                $table->string('country', 100)->default('Saudi Arabia');
                $table->string('star_rating', 50)->nullable()->default('4 Star'); // 5 Star, 4 Star, 3 Star, 2 Star, Standard / Economy
                $table->string('distance', 191)->nullable(); // e.g. "50m from Haram", "Clock Tower", "Walking Distance"
                $table->text('address')->nullable();
                $table->string('contact_number', 50)->nullable();
                $table->string('email', 100)->nullable();
                $table->text('notes')->nullable();
                $table->boolean('status')->default(true);
                $table->timestamps();
            });

            // Seed popular high-demand pilgrimage hotels
            $now = now();
            $seedHotels = [
                // Makkah Hotels
                ['name' => 'Makkah Clock Royal Tower (Fairmont)', 'city' => 'Makkah', 'country' => 'Saudi Arabia', 'star_rating' => '5 Star', 'distance' => '0m - Clock Tower, Direct Haram View', 'address' => 'King Abdul Aziz Endowment, Abraj Al Bait, Makkah', 'contact_number' => '+966 12 571 7777', 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
                ['name' => 'Pullman Zamzam Makkah', 'city' => 'Makkah', 'country' => 'Saudi Arabia', 'star_rating' => '5 Star', 'distance' => '0m - Abraj Al Bait Complex', 'address' => 'Abraj Al Bait Complex, King Abdul Aziz Endowment, Makkah', 'contact_number' => '+966 12 571 5555', 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
                ['name' => 'Swissôtel Makkah', 'city' => 'Makkah', 'country' => 'Saudi Arabia', 'star_rating' => '5 Star', 'distance' => '0m - Direct Courtyard Access', 'address' => 'King Abdul Aziz Endowment, Abraj Al Bait, Makkah', 'contact_number' => '+966 12 571 8000', 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
                ['name' => 'Jabal Omar Hyatt Regency Makkah', 'city' => 'Makkah', 'country' => 'Saudi Arabia', 'star_rating' => '5 Star', 'distance' => '100m from Haram', 'address' => 'Jabal Omar, Ibrahim Al Khalil St, Makkah', 'contact_number' => '+966 12 577 1234', 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
                ['name' => 'Jabal Omar Marriott Hotel', 'city' => 'Makkah', 'country' => 'Saudi Arabia', 'star_rating' => '5 Star', 'distance' => '250m from Haram Courtyard', 'address' => 'Jabal Omar, Umm Al Qura St, Makkah', 'contact_number' => '+966 12 571 9999', 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
                ['name' => 'Al Safwah Royale Orchid', 'city' => 'Makkah', 'country' => 'Saudi Arabia', 'star_rating' => '5 Star', 'distance' => 'Opposite King Abdulaziz Gate (0m)', 'address' => 'Ajyad Street, Front of Haram, Makkah', 'contact_number' => '+966 12 576 8888', 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
                ['name' => 'Voco Makkah (IHG)', 'city' => 'Makkah', 'country' => 'Saudi Arabia', 'star_rating' => '4 Star', 'distance' => '1.3km - 24/7 Free Haram Shuttle', 'address' => 'Ibrahim Al Khalil Street, Makkah', 'contact_number' => '+966 12 526 2555', 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
                ['name' => 'Emaar Grand Hotel', 'city' => 'Makkah', 'country' => 'Saudi Arabia', 'star_rating' => '4 Star', 'distance' => '750m from Haram', 'address' => 'Ibrahim Al Khalil Rd, Al Misfalah, Makkah', 'contact_number' => '+966 12 537 0000', 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
                ['name' => 'Kiswah Towers Makkah', 'city' => 'Makkah', 'country' => 'Saudi Arabia', 'star_rating' => '3 Star', 'distance' => '900m - Free Shuttle Bus', 'address' => 'At Taysir, Jarwal, Makkah', 'contact_number' => '+966 12 541 2222', 'status' => 1, 'created_at' => $now, 'updated_at' => $now],

                // Madinah Hotels
                ['name' => 'The Oberoi Madinah', 'city' => 'Madinah', 'country' => 'Saudi Arabia', 'star_rating' => '5 Star', 'distance' => '0m - Adjacent to Prophet\'s Mosque Courtyard', 'address' => 'Northern Central Area, Madinah', 'contact_number' => '+966 14 828 2222', 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
                ['name' => 'Dar Al Taqwa Hotel', 'city' => 'Madinah', 'country' => 'Saudi Arabia', 'star_rating' => '5 Star', 'distance' => '0m - Opposite Prophet\'s Mosque Main Gate', 'address' => 'Off King Fahd Road, Central Area, Madinah', 'contact_number' => '+966 14 829 1111', 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
                ['name' => 'Anwar Al Madinah Mövenpick Hotel', 'city' => 'Madinah', 'country' => 'Saudi Arabia', 'star_rating' => '5 Star', 'distance' => 'Direct access to Ladies & Gents Gates', 'address' => 'Central Zone, Madinah', 'contact_number' => '+966 14 818 1000', 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
                ['name' => 'Pullman Zamzam Madina', 'city' => 'Madinah', 'country' => 'Saudi Arabia', 'star_rating' => '5 Star', 'distance' => '150m from Bab Al Salam', 'address' => 'Amr Bin Al Ghas Street, Central Area, Madinah', 'contact_number' => '+966 14 821 0500', 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
                ['name' => 'Madinah Hilton Hotel', 'city' => 'Madinah', 'country' => 'Saudi Arabia', 'star_rating' => '5 Star', 'distance' => 'Walking Distance to Haram (100m)', 'address' => 'King Fahd Road, Central Area, Madinah', 'contact_number' => '+966 14 820 1000', 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
                ['name' => 'Rove Al Madinah Hotel', 'city' => 'Madinah', 'country' => 'Saudi Arabia', 'star_rating' => '4 Star', 'distance' => '200m from Haram Northern Courtyard', 'address' => 'Northern Central Area, Madinah', 'contact_number' => '+966 14 829 2000', 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
                ['name' => 'Saja Al Madinah Hotel', 'city' => 'Madinah', 'country' => 'Saudi Arabia', 'star_rating' => '4 Star', 'distance' => '350m from Prophet\'s Mosque', 'address' => 'Northern Central Area, Madinah', 'contact_number' => '+966 14 829 5555', 'status' => 1, 'created_at' => $now, 'updated_at' => $now],

                // Jeddah Hotels
                ['name' => 'Radisson Blu Hotel, Jeddah Airport', 'city' => 'Jeddah', 'country' => 'Saudi Arabia', 'star_rating' => '5 Star', 'distance' => 'Near King Abdulaziz International Airport', 'address' => 'Madinah Road, Jeddah', 'contact_number' => '+966 12 510 5000', 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
                ['name' => 'Jeddah Hilton Hotel', 'city' => 'Jeddah', 'country' => 'Saudi Arabia', 'star_rating' => '5 Star', 'distance' => 'Corniche Road', 'address' => 'North Corniche Road, Jeddah', 'contact_number' => '+966 12 224 4444', 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
            ];

            DB::table('hotels')->insert($seedHotels);
        }

        // Add Hotel Permissions under Preferences
        if (Schema::hasTable('permissions')) {
            $now = now();
            $pref = DB::table('permissions')->where('name', 'Preferences')->first();
            $prefId = $pref ? $pref->id : 0;

            $hotelPerms = [
                'Hotels list',
                'Hotels create',
                'Hotels edit',
                'Hotels delete'
            ];

            foreach ($hotelPerms as $permName) {
                $exists = DB::table('permissions')->where('name', $permName)->first();
                if (!$exists) {
                    $pId = DB::table('permissions')->insertGetId([
                        'name' => $permName,
                        'guard_name' => 'web',
                        'parent_id' => $prefId,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);

                    // Assign to Admin and Super-Admin roles automatically
                    if (Schema::hasTable('roles') && Schema::hasTable('role_has_permissions')) {
                        $roleIds = DB::table('roles')->whereIn('name', ['Admin', 'Super-Admin', 'admin', 'super-admin'])->pluck('id');
                        foreach ($roleIds as $rId) {
                            DB::table('role_has_permissions')->insertOrIgnore([
                                'permission_id' => $pId,
                                'role_id' => $rId,
                            ]);
                        }
                    }
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotels');
    }
};
