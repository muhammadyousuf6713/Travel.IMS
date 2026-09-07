<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAboutDetailsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('about_details')) {
            Schema::create('about_details', function (Blueprint $table) {
                $table->id();
                $table->foreignId('about_header_id')
                      ->constrained('about_header')
                      ->onDelete('cascade');
                $table->string('title');
                $table->string('name')->nullable();
                $table->string('image');
                $table->text('detail')->nullable();
                $table->date('from_date')->nullable();
                $table->date('to_date')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('about_details');
    }
}
