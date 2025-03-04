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
        Schema::create('tbl_sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->nullable()->constrained('tbl_leads')->onDelete('set null');
            $table->string('stage', 50);
            $table->integer('probability')->nullable();
            $table->date('forecast_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_sales');
    }
};
