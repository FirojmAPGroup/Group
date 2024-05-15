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
        Schema::create('business', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('owner_first_name')->nullable();
            $table->string('owner_last_name')->nullable();
            $table->string('owner_email',125)->nullable();
            $table->string('owner_number')->nullable();
            $table->tinyInteger('ti_status')->default(2);
            $table->integer('pincode')->nullable();
            $table->string('city',45)->nullable();
            $table->string('state',45)->nullable();
            $table->string('country',50)->default('India');
            $table->string('area',50)->default('India');
            $table->double('latitude', 10, 6);
            $table->double('longitude', 10, 6);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business');
    }
};
