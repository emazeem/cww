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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->integer('order_id');
            $table->date('date');
            $table->time('time');
            $table->integer('status')->default(0);
            $table->string('approval')->comment('Pending0 Approved1 RescheduleRequest2 Rescheduled3')->default(0);
            $table->integer('inside_wash')->default(0);
            $table->integer('outside_wash')->default(0);
            $table->string('inside_status')->nullable();
            $table->string('outside_status')->nullable();
            $table->string('comments')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
