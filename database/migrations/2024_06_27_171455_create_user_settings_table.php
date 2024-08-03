<?php

use App\Constants\InvoiceThemeConstants;
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
        Schema::create('user_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->string('name')->nullable();
            $table->string('managing_director')->nullable();
            $table->string('legal_name')->nullable();
            $table->string('avatar_url')->nullable();
            $table->string('website_url')->nullable();
            $table->string('place_of_jurisdiction')->nullable();
            $table->string('slug')->nullable();
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
            
            // other settings
            $table->boolean('ready_to_generate')->default(0);
            $table->decimal('vat_percent', 5, 2)->default(0.00);
            $table->string('invoice_language')->default('de');
            $table->unsignedBigInteger('currency_id')->default(96);
            $table->unsignedBigInteger('invoice_theme_id')->default(InvoiceThemeConstants::DEFAULTID);

            // time
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_settings');
    }
};
