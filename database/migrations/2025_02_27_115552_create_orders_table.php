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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->json('products'); 
            $table->string('city');
            $table->string('address');
            $table->string('building_number');
            $table->enum('status', ['pending', 'shipped', 'delivered'])->default('pending');
            $table->enum('payment_status', ['paid', 'not_paid'])->default('not_paid');
            $table->enum('payment_method', ['stripe', 'paypal'])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
