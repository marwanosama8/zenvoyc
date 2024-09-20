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
        Schema::create('company_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->decimal('vat_percent', 5, 2)->default(0.00);
            $table->string('invoice_language')->default('de');
            $table->unsignedBigInteger('currency_id')->default(96);
            $table->unsignedBigInteger('invoice_theme_id')->default(InvoiceThemeConstants::DEFAULT_ID);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_settings');
    }
};
