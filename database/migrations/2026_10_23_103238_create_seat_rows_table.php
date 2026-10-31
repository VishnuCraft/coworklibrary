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
        Schema::create('seat_rows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('space_id')->constrained()->cascadeOnDelete();
            $table->string('section_name')->default('Main Area'); // e.g. "Hall", "Kitchen", "Cabin 1"
            $table->string('row_label'); // e.g. "Row 1", "Row A"
            $table->integer('row_order')->default(0);
            $table->integer('total_columns')->default(10);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seat_rows');
    }
};
