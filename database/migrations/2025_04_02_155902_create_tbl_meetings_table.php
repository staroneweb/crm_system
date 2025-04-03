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
        Schema::create('tbl_meetings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lead_id')->nullable(); // Make lead_id nullable
            $table->dateTime('meeting_date');
            $table->string('location')->nullable();
            $table->text('agenda')->nullable();
            $table->timestamps();

            // Foreign key with SET NULL on delete
            $table->foreign('lead_id')->references('id')->on('tbl_leads')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_meetings');
    }
};
