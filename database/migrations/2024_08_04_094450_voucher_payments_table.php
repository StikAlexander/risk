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
        Schema::create('voucher_payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('invoice_id');
            $table->date('payment_date');
            $table->decimal('amount', 10, 2);
            $table->string('voucher');
            $table->string('epayco_ref')->nullable();
            $table->string('epayco_transaction_id')->nullable();
            $table->timestamp('epayco_transaction_date')->nullable();
            $table->string('pdf_path')->nullable();
            $table->timestamps();

            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voucher_payments');
    }
};
