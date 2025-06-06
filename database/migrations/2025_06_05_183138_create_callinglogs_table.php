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
        Schema::create('callinglogs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id')->constrained('registers')->onDelete('cascade');
            $table->foreignId('staff_id')->constrained('staffs')->onDelete('cascade');
            $table->integer('not_recieved')->default(0);
            $table->integer('hot_client')->default(0);
            $table->integer('not_required')->default(0);
            $table->integer('demo')->default(0);
            $table->integer('total_calling')->default(0);
            $table->text('work_remark')->nullable();
            $table->integer('support')->default(0);
            $table->text('support_remark')->nullable();
            $table->integer('installation')->default(0);
            $table->text('install_remark')->nullable();
            $table->integer('demo_given')->default(0);
            $table->text('demo_remark')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('callinglogs');
    }
};
