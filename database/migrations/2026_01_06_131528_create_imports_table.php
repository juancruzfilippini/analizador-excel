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
        Schema::create('imports', function (Blueprint $table) {
    $table->id();
    $table->string('original_name');
    $table->string('stored_path');
    $table->unsignedInteger('rows_total')->default(0);
    $table->unsignedInteger('rows_ok')->default(0);
    $table->unsignedInteger('rows_failed')->default(0);
    $table->string('status')->default('uploaded'); // uploaded|processing|done|failed
    $table->text('notes')->nullable(); // logs resumidos
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('imports');
    }
};
