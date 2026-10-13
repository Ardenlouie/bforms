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
        Schema::create('supplier_invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('form_id')->nullable();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->string('control_number')->nullable();
            $table->date('date_received')->nullable();
            $table->string('supplier_name')->nullable();
            $table->string('invoice_number')->nullable();
            $table->decimal('invoice_amount', 15, 2)->nullable();
            $table->string('payment_terms')->nullable();
            $table->date('due_date')->nullable();
            $table->date('date_forwarded')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->unsignedBigInteger('receive_id')->nullable();
            $table->unsignedBigInteger('rfp_form_id')->nullable();
            $table->date('finance_submit')->nullable();
            $table->date('check_date')->nullable();
            $table->string('bank_branch')->nullable();
            $table->date('date_posted')->nullable();
            $table->date('department_date')->nullable();
            $table->string('department_status')->nullable();
            $table->date('ap_date')->nullable();
            $table->string('ap_status')->nullable();
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
        Schema::dropIfExists('supplier_invoices');
    }
};
