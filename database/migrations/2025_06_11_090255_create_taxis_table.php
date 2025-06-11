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
        Schema::create('taxis', function (Blueprint $table) {
            $table->id();
            $table->string('license_plate', 50)->unique();
            $table->string('model', 100)->nullable();
            $table->enum('type', ['Standard', 'Van', 'Luxury']);
            $table->string('city', 100)->nullable();
            $table->integer('capacity');
            $table->foreignId('driver_id')->nullable()->constrained('users')->onDelete('set null');
            $table->boolean('is_available')->default(true);

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
        Schema::dropIfExists('taxis');
    }
};
