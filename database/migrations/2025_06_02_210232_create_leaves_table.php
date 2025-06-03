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
        Schema::create('leaves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id')->constrained('registers');
            $table->foreignId('staff_id')->constrained('staffs');
            $table->date('apply_date');
            $table->date('from_date');
            $table->date('to_date');
            $table->integer('total_days')->nullable();
            $table->json('reason');
            $table->string('approve_status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leaves');
    }
};
