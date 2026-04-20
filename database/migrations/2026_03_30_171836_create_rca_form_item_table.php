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
        Schema::create('rca_form_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rca_form_id')->nullable();
            $table->string('item_description')->nullable();
            $table->decimal('amount', 15, 2)->nullable();
            $table->decimal('days', 10, 2)->nullable();
            $table->string('remarks')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rca_form_items');
    }
};
