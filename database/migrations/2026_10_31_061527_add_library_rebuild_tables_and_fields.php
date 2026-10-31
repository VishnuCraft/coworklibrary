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
        // 1. Add fields to properties (libraries)
        Schema::table('properties', function (Blueprint $table) {
            $table->string('area')->nullable()->after('address');
            $table->string('city')->nullable()->after('area');
            $table->string('phone')->nullable()->after('city');
            $table->string('upi_id')->nullable()->after('phone');
            $table->string('open_time')->default('6:00 AM')->after('upi_id');
            $table->string('close_time')->default('10:00 PM')->after('open_time');
            $table->json('shifts')->nullable()->after('close_time');
            $table->json('facilities')->nullable()->after('shifts');
        });

        // 2. Add AC and Aisle fields to spaces (halls)
        Schema::table('spaces', function (Blueprint $table) {
            $table->boolean('is_ac')->default(true)->after('name');
            $table->integer('aisle_position')->default(5)->after('sort_order');
        });

        // 3. Create students table
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->foreignId('space_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('seat_id')->nullable()->constrained()->nullOnDelete();
            $table->string('shift')->default('morning'); // full, morning, evening
            $table->string('name');
            $table->string('phone');
            $table->string('parent_phone')->nullable();
            $table->string('exam')->nullable();
            $table->decimal('monthly_fee', 10, 2)->default(0);
            $table->date('joined_on');
            $table->date('paid_until');
            $table->string('status')->default('active'); // active, left
            $table->date('left_on')->nullable();
            $table->timestamps();
        });

        // 4. Create payments table
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('property_id')->nullable()->constrained()->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->integer('months')->default(1);
            $table->string('mode')->default('UPI'); // Cash, UPI
            $table->date('paid_on');
            $table->string('notes')->nullable();
            $table->timestamps();
        });

        // 5. Add fields to bookings table
        Schema::table('bookings', function (Blueprint $table) {
            $table->foreignId('student_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->string('shift')->default('morning')->after('seat_number');
            $table->string('customer_name')->nullable()->after('shift');
            $table->string('customer_phone')->nullable()->after('customer_name');
            $table->string('parent_phone')->nullable()->after('customer_phone');
            $table->string('exam')->nullable()->after('parent_phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['student_id']);
            $table->dropColumn(['student_id', 'shift', 'customer_name', 'customer_phone', 'parent_phone', 'exam']);
        });

        Schema::dropIfExists('payments');
        Schema::dropIfExists('students');

        Schema::table('spaces', function (Blueprint $table) {
            $table->dropColumn(['is_ac', 'aisle_position']);
        });

        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn(['area', 'city', 'phone', 'upi_id', 'open_time', 'close_time', 'shifts', 'facilities']);
        });
    }
};
