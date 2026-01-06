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
        Schema::create('school_snapshots', function (Blueprint $table) {
    $table->id();
    $table->foreignId('import_id')->constrained()->cascadeOnDelete();
    $table->foreignId('school_id')->constrained()->cascadeOnDelete();

    // algunos campos típicos del excel ejemplo
    $table->unsignedInteger('enrollment')->nullable(); // matrícula
    $table->decimal('mb_calculated', 10, 2)->nullable(); // MB calculado
    $table->string('connectivity_status')->nullable(); // Estado Conectividad
    $table->string('lan_status')->nullable(); // Estado red local

    $table->json('raw')->nullable(); // opcional: guardar fila cruda para debugging
    $table->timestamps();

    $table->index(['import_id', 'school_id']);
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_snapshots');
    }
};
