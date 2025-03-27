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
        Schema::table('tbl_leads', function (Blueprint $table) {
            // Drop the existing 'stage' column if it exists
            if (Schema::hasColumn('tbl_leads', 'stage')) {
                $table->dropColumn('stage');
            }

            // Ensure 'lead_stage' column is UNSIGNED and matches 'tbl_stage.id'
            $table->unsignedBigInteger('lead_stage')->nullable()->after('id');

            // Set up foreign key constraint
            $table->foreign('lead_stage')
                ->references('id')
                ->on('tbl_stage')
                ->onDelete('SET NULL'); // Ensures data integrity on deletion
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_leads', function (Blueprint $table) {
            //
        });
    }
};
