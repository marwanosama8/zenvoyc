<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('tenant_invoices', function (Blueprint $table) {
            $table->id();
            $table->morphs('invoiceable');
            $table->unsignedBigInteger('customer_id');
            $table->string('rgnr', 50);
            $table->text('customer_address')->nullable();
            $table->date('date_origin');
            $table->date('date_start');
            $table->date('date_end');
            $table->date('date_pay');
            $table->decimal('rate', 8, 2)->nullable();
            $table->text('info')->nullable();
            $table->float('vat_percent')->default(0.00);
            $table->boolean('has_vat')->default(1);
            $table->boolean('send')->default(0);
            $table->boolean('payed')->default(0);
            $table->timestamps(); // This will automatically add created_at and updated_at
            $table->softDeletes(); // This will add deleted_at column for soft deletes
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_invoices');
    }
};
