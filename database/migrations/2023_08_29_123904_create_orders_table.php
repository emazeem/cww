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
            $table->integer('car_id');
            $table->integer('subscription_id');
            $table->string('price');
            $table->string('payment')->default(0);
            $table->date('payment_date')->nullable();
            $table->string('payment_type')->nullable();
            $table->longText('receipt')->nullable();
            $table->integer('status')->default(0);
            $table->date('renew_on')->nullable();
            $table->string('order_id')->nullable();
            $table->string('type')->nullable();
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
