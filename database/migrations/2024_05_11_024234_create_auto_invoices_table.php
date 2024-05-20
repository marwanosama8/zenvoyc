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
            $table->string('rgnr', 50);
            $table->text('customer_address')->nullable();
            $table->decimal('rate', 8, 2)->nullable();
            $table->text('info')->nullable();
            $table->longText('options')->nullable();
            $table->json('items')->nullable();
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
