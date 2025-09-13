<?php

declare(strict_types=1);

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
        Schema::create('modifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('car_id')->constrained('cars')->onDelete('cascade');
            $table->string('name');
            $table->string('category'); // TODO create categories table
            $table->text('notes')->nullable();
            $table->string('brand')->nullable();
            $table->string('vendor')->nullable();
            $table->dateTime('installation_date')->nullable();
            $table->float('cost')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // TODO fields to be added later
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modifications');
    }
};
