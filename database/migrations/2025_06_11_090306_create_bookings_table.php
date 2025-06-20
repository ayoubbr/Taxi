<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->uuid('booking_uuid')->unique(); // Using UUID for QR Code
            $table->foreignId('client_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('client_name', 100);
            $table->string('pickup_location');
            // $table->string('pickup_city', 100)->nullable();
            $table->foreignId('pickup_city_id')->constrained('cities')->onDelete('cascade');
            $table->foreignId('destination_city_id')->constrained('cities')->onDelete('cascade');
            // $table->string('destination');
            $table->dateTime('pickup_datetime');
            $table->enum('status', ['PENDING', 'ASSIGNED', 'IN_PROGRESS', 'COMPLETED', 'CANCELLED', 'NO_TAXI_FOUND'])->default('PENDING');
            $table->foreignId('assigned_taxi_id')->nullable()->constrained('taxis')->onDelete('set null');
            $table->foreignId('assigned_driver_id')->nullable()->constrained('users')->onDelete('set null');
            $table->decimal('estimated_fare', 10, 2)->nullable();
            $table->json('qr_code_data')->nullable();
            $table->integer('search_tier')->nullable();
            $table->enum('taxi_type', ['standard', 'van', 'luxe'])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bookings');
    }
};
