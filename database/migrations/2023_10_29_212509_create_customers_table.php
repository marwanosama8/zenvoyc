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
        Schema::create('customers', function (Blueprint $table) {
            $table->id(); // bigIncrements
            $table->morphs('customerable');
            $table->string('name', 200);
            $table->text('added')->nullable();
            $table->string('street', 100);
            $table->string('token', 100);
            $table->string('nr', 20);
            $table->string('zip', 20);
            $table->string('city', 100);
            $table->string('country', 100);
            $table->string('contact', 100)->nullable();
            $table->string('email', 200)->nullable();
            $table->string('cc', 200)->nullable();
            $table->string('vatid', 50)->nullable();
            $table->decimal('rate', 8, 2)->default(100.00);
            $table->longText('options')->nullable();
            $table->timestamps(); // This will automatically add created_at and updated_at
            $table->softDeletes(); // This will add deleted_at column for soft deletes
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
