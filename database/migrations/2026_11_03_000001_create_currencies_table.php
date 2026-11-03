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
        if (!Schema::hasTable('currencies')) {
            Schema::create('currencies', function (Blueprint $table) {
                $table->id();
                $table->string('code', 10)->unique();
                $table->string('name', 100);
                $table->string('symbol', 20)->default('');
                $table->decimal('exchange_rate', 14, 4)->default(1.0000);
                $table->boolean('is_default')->default(false);
                $table->boolean('status')->default(true);
                $table->timestamps();
            });

            DB::table('currencies')->insert([
                ['code' => 'SAR', 'name' => 'Saudi Riyal', 'symbol' => 'SAR', 'exchange_rate' => 75.5000, 'is_default' => 0, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
                ['code' => 'PKR', 'name' => 'Pakistani Rupee', 'symbol' => 'PKR', 'exchange_rate' => 1.0000, 'is_default' => 1, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
                ['code' => 'USD', 'name' => 'US Dollar', 'symbol' => '$', 'exchange_rate' => 280.0000, 'is_default' => 0, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
                ['code' => 'AED', 'name' => 'UAE Dirham', 'symbol' => 'AED', 'exchange_rate' => 76.2000, 'is_default' => 0, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
                ['code' => 'EUR', 'name' => 'Euro', 'symbol' => '€', 'exchange_rate' => 305.0000, 'is_default' => 0, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
                ['code' => 'GBP', 'name' => 'British Pound', 'symbol' => '£', 'exchange_rate' => 355.0000, 'is_default' => 0, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
