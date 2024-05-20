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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            // identification
            $table->string('name');
            $table->string('managing_director')->nullable();
            $table->string('legal_name')->nullable();
            $table->string('avatar_url')->nullable();
            $table->string('website_url')->nullable();
            $table->string('place_of_jurisdiction')->nullable();
            $table->string('slug');
            // address
            $table->string('address')->nullable();
            $table->string('postal_code')->nullable();
            // back info
            $table->string('tax_id')->nullable();
            $table->string('vat_id')->nullable();
            $table->string('iban')->nullable();
            $table->string('account_number')->nullable();
            $table->string('bank_code')->nullable();
            $table->string('bic')->nullable();
            // contact info
            $table->string('contact_number')->nullable();
            $table->string('contact_email')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
