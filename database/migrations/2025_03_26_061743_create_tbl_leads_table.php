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
            $table->string('name'); // Required field
            $table->text('address'); // Required field
            $table->string('email')->unique(); // Required field
            $table->string('contact'); // Required field
            
            $table->foreignId('lead_source')->constrained('tbl_source')->onDelete('cascade'); // Foreign Key to source table
            $table->foreignId('lead_status')->constrained('tbl_stage')->onDelete('cascade'); // Foreign Key to stage table
            
            $table->string('company_name')->nullable();
            $table->string('company_website')->nullable();
            
            $table->decimal('opportunity_amount', 10, 2)->nullable(); // Opportunity Amount
            $table->text('description')->nullable(); // Lead description
            $table->string('referred_by')->nullable(); // Reference
            
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
