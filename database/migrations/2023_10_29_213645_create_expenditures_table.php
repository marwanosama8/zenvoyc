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
        Schema::create('expenditures', function (Blueprint $table) {
            $table->id();
            $table->morphs('expenditureable');
            $table->string('name', 200);
            $table->date('start');
            $table->date('end');
            $table->text('description')->nullable();
            $table->decimal('cost', 10, 2);
            $table->enum('frequency', ['one-time', 'monthly', 'quarterly', 'yearly']);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenditures');
    }
};
