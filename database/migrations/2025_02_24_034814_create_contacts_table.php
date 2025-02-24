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
        Schema::create('tbl_contacts', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 100)->notNull();
            $table->string('last_name', 100)->notNull();
            $table->string('email', 150)->unique()->notNull();
            $table->string('phone', 20)->nullable();
            $table->string('company', 150)->nullable();
            $table->enum('status', ['lead', 'customer', 'archived'])->default('lead');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('assigned_to');
            $table->foreign('created_by')->references('id')->on('tbl_users')->onDelete('cascade');
            $table->foreign('assigned_to')->references('id')->on('tbl_users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
