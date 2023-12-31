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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone');
            $table->string('address');
            $table->string('profile')->nullable();
            $table->enum('role',['manager','technician','customer']);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('long')->nullable();
            $table->string('lat')->nullable();
            $table->string('customer_id')->nullable();
            $table->integer('group_id')->comment('For customers to grouped for technicians. Tech can have many customers.');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
