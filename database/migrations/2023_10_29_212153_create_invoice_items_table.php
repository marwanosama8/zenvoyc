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
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invoice_id');
            $table->text('description');
            $table->decimal('amount', 8, 2)->default(0.00);
            $table->tinyInteger('type')->default(1);
            $table->decimal('price', 10, 2);
            $table->tinyInteger('notInvoiced')->default(0);
            $table->integer('order_column')->nullable();
            $table->timestamps(); // This will automatically add created_at and updated_at
            $table->softDeletes(); // This adds the deleted_at column for soft deletes
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};
