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
        Schema::create('auto_invoices', function (Blueprint $table) {
            $table->id();
            $table->morphs('auto_invoiceable');
            $table->unsignedBigInteger('customer_id');
            $table->text('info')->nullable();
            $table->json('items')->nullable();
            $table->boolean('has_vat')->default(1);
            $table->tinyInteger('custom_interval');
            $table->date('next_generate_date');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auto_invoices');
    }
};
