<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('booking_trx_id');
            $table->string('name');
            $table->string('phone');
            $table->string('email');
            $table->string('customer_bank_name');
            $table->string('customer_bank_number');
            $table->string('customer_bank_account');
            $table->string('proof'); 
            $table->unsignedBigInteger('total_amount');
            $table->unsignedBigInteger('duration'); // bisa dalam hari, jam, atau menit sesuai sistem
            $table->unsignedBigInteger('total_tax_amount')->nullable();
            $table->unsignedBigInteger('price');
            $table->boolean('is_paid');
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_subscriptions');
    }
};
