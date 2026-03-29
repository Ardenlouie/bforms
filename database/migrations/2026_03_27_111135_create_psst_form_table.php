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
        Schema::create('psst_forms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('form_id')->nullable();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->string('control_number')->nullable();
            $table->string('point_origin')->nullable();
            $table->string('delivery_instructions')->nullable();
            $table->string('objective')->nullable();
            $table->date('date_submitted')->nullable();
            $table->date('delivery_date')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('psst_forms');
    }
};
