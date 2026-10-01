<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Reviews module: one review record per completed inquiry + its follow-up history.
 * No foreign keys on purpose (legacy tables use mixed int types); indexed instead.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('inquiry_reviews')) {
            Schema::create('inquiry_reviews', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('inquiry_id')->unique();
                $table->unsignedBigInteger('customer_id')->nullable()->index();
                $table->unsignedBigInteger('assigned_to')->nullable();
                $table->unsignedBigInteger('assigned_by')->nullable();
                $table->string('source', 20)->default('manual');          // auto | manual
                $table->string('status', 30)->default('pending');          // pending | requested | not_reachable | received | declined
                $table->dateTime('completed_at')->nullable();              // when the inquiry was completed
                $table->date('due_date')->nullable();                      // completed_at + N days
                $table->date('next_followup_date')->nullable();
                $table->unsignedInteger('attempts')->default(0);
                $table->dateTime('last_contacted_at')->nullable();
                $table->unsignedTinyInteger('rating')->nullable();         // 1..5
                $table->json('platforms')->nullable();                     // ["Google","Facebook",...]
                $table->string('review_link', 500)->nullable();
                $table->text('customer_comment')->nullable();
                $table->dateTime('received_at')->nullable();
                $table->text('note')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index(['assigned_to', 'status']);
                $table->index('next_followup_date');
            });
        }

        if (!Schema::hasTable('inquiry_review_followups')) {
            Schema::create('inquiry_review_followups', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('review_id')->index();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('channel', 30)->nullable();                 // WhatsApp | Call | SMS | Email | In Person
                $table->string('outcome', 30);                             // link_sent | will_do_later | not_reachable | received | declined | note
                $table->unsignedTinyInteger('rating')->nullable();
                $table->json('platforms')->nullable();
                $table->string('review_link', 500)->nullable();
                $table->text('remarks')->nullable();
                $table->date('next_followup_date')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('inquiry_review_followups');
        Schema::dropIfExists('inquiry_reviews');
    }
};
