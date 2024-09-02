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
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->morphs('offerable');
            $table->string('token');
            $table->boolean('general_access')->default(1);
            $table->string('title');
            $table->longText('introtext')->nullable(); // Defining contact type
            $table->json('positions')->nullable();
            $table->longText('signature')->nullable();
            $table->string('signature_name')->nullable();
            $table->date('signature_date')->nullable();
            $table->boolean('signed')->default(0);
            //new
            $table->boolean('accepted')->default(0);
            $table->boolean('external_offer')->default(0);
            $table->string('external_offer_url')->nullable();
            $table->string('offer_value')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
};
