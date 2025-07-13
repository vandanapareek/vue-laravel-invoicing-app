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
        Schema::create('invoices', function (Blueprint $table) {
            $table->string('id', 6)->primary();
            $table->string('clientName');
            $table->string('clientEmail')->nullable();
            $table->string('paymentTerms');
            $table->timestamp('createdAt');
            $table->timestamp('paymentDue');
            $table->json('items');
            $table->string('status');
            $table->decimal('total', 10, 2);
            $table->json('senderAddress')->nullable();
            $table->json('clientAddress')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
