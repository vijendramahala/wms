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
        Schema::create('prospects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id')->constrained('registers');
            $table->foreignId('staff_id')->constrained('staffs');
            $table->string('priority'); // ✅ Spelling corrected
            $table->string('prospect_name');
            $table->string('mobile_no');
            $table->string('alternative_no')->nullable(); // ✅ Spelling corrected + nullable() fix
            $table->string('city')->nullable();
            $table->string('district')->nullable();
            $table->string('state')->nullable();
            $table->string('address');
            $table->string('product');
            $table->string('variant');
            $table->decimal('software_price', 15, 2); // ✅ Better for price values (numeric)
            $table->date('date');
            $table->time('time');
            $table->text('remark'); // ✅ Changed to text in case remarks are long
            $table->text('demo_details'); // ✅ Same here
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prospects');
    }
};
