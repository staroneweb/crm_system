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
        Schema::create('tbl_leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_id')->constrained('tbl_contacts')->onDelete('cascade'); // Foreign Key to contacts table
            $table->string('source', 100)->nullable(); // VARCHAR(100) NULL
            $table->enum('stage', ['new', 'qualified', 'proposal', 'won', 'lost'])->default('new'); // ENUM with default
            $table->decimal('value', 10, 2)->nullable(); // DECIMAL(10,2) NULL
            $table->foreignId('assigned_to')->nullable()->constrained('tbl_users')->onDelete('set null'); // Foreign Key to users table
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_leads');
    }
};
