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
        Schema::create('seats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('space_id')->constrained()->cascadeOnDelete();
            $table->foreignId('seat_row_id')->nullable()->constrained('seat_rows')->cascadeOnDelete();
            $table->string('seat_number'); // e.g. "A1", "R1-01", "Bed 1"
            $table->integer('column_index')->default(1);
            $table->string('type')->default('seat'); // seat, desk, bed, cabin
            $table->boolean('is_walkway')->default(false); // aisle / walking space
            $table->string('status')->default('available'); // available, occupied, reserved, maintenance
            $table->decimal('price', 10, 2)->nullable();
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seats');
    }
};
