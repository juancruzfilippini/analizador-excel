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
        Schema::create('schools', function (Blueprint $table) {
    $table->id();
    $table->string('cue')->unique(); // CUE Predio
    $table->string('name')->nullable(); // si existe en el excel, sino lo dejamos
    $table->string('province')->nullable();
    $table->string('department')->nullable();
    $table->string('city')->nullable();
    $table->string('address')->nullable();
    $table->decimal('lat', 10, 7)->nullable();
    $table->decimal('lng', 10, 7)->nullable();
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schools');
    }
};
