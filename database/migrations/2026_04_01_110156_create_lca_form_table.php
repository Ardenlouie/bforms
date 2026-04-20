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
        Schema::create('lca_forms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('form_id')->nullable();
            $table->unsignedBigInteger('rca_form_id')->nullable();
            $table->string('control_number')->nullable();
            $table->string('file_name')->nullable();
            $table->string('path')->nullable();
            $table->decimal('total_amount', 15,2)->nullable();
            $table->decimal('balance', 15,2)->nullable();
            $table->date('date_submitted')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lca_forms');
    }
};
