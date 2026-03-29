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
        Schema::create('gate_pass_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gate_pass_id')->nullable();
            $table->string('item_description')->nullable();
            $table->string('uom')->nullable();
            $table->decimal('quantity', 10, 2)->nullable();
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
        Schema::dropIfExists('gate_pass_items');
    }
};
