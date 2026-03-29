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
        Schema::create('psrf_forms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('form_id')->nullable();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->string('control_number')->nullable();
            $table->string('recipient')->nullable();
            $table->string('activity_name')->nullable();
            $table->string('objective')->nullable();
            $table->string('special_instructions')->nullable();
            $table->date('date_submitted')->nullable();
            $table->date('program_date')->nullable();

            $table->timestamps();
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('psrf_forms');
    }
};
