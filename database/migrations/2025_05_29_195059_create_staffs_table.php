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
        Schema::create('staffs', function (Blueprint $table) {
            $table->id();
            $table->string('staff_name');
            $table->string('father_name');
            $table->string('mobile_no');
            $table->integer('pin_code');
            $table->string('state');
            $table->string('city');
            $table->string('address');
            $table->string('sales_man');
            $table->string('sales_executive');
            $table->string('password');
            $table->date('joining_date');
            $table->date('resignation_date')->nullable();
            $table->integer('user_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staffs');
    }
};
