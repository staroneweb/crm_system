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
        Schema::create('tbl_opportunities', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('lead_id')->nullable();
            $table->string('opportunity_name');
            $table->date('expected_close_date')->nullable();
            $table->enum('status', ['open', 'hot', 'cold', 'inprocess'])->default('open');
            $table->decimal('opp_amount', 10, 2)->nullable();
            $table->integer('probability')->nullable(); // Probability as percentage (0-100)
            $table->unsignedBigInteger('assigned_to')->nullable(); // Assigned user
            $table->text('description')->nullable();
            $table->timestamps(); // Includes `created_at` and `updated_at`

            // Foreign keys
            $table->foreign('lead_id')->references('id')->on('tbl_leads')->onDelete('set null');
            $table->foreign('assigned_to')->references('id')->on('tbl_users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_opportunities', function (Blueprint $table) {
            //
        });
    }
};
