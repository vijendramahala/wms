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
        Schema::create('demodones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id')->constrained('registers')->onDelete('cascade');
            $table->foreignId('staff_id')->constrained('staffs')->onDelete('cascade');
            $table->date('date');
            $table->string('prospect_name');
            $table->string('product');
            $table->string('staff_name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('demodones');
    }
};
