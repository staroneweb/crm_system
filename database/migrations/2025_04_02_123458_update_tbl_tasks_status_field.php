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
        Schema::table('tbl_tasks', function (Blueprint $table) {

            // Remove old fields
            $table->dropColumn(['title', 'description','status']);

            // Add new fields
            $table->unsignedBigInteger('lead_id')->nullable()->after('id');
            $table->string('task_name')->after('lead_id');
            $table->text('task_description')->nullable()->after('task_name');
            $table->dateTime('start_datetime')->nullable()->after('task_description'); // Start date & time
            $table->integer('duration')->nullable()->after('start_datetime'); // Duration in minutes
            // Add status_id as a foreign key instead of ENUM
            $table->unsignedBigInteger('status_id')->nullable()->after('task_description');
            $table->foreign('status_id')->references('id')->on('tbl_lead_status')->onDelete('set null');

            // Add foreign key for lead_id
            $table->foreign('lead_id')->references('id')->on('tbl_leads')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_tasks', function (Blueprint $table) {
            // Rollback: Remove newly added columns
            $table->dropForeign(['lead_id']);
            $table->dropColumn('lead_id');

            $table->dropForeign(['status_id']);
            $table->dropColumn('status_id');

            $table->dropColumn('task_name');
            $table->dropColumn('task_description');
            $table->dropColumn('start_datetime');
            $table->dropColumn('duration');

            // Restore old fields
            $table->string('title')->after('id');
            $table->text('description')->nullable()->after('title');
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending')->after('duration');
        });
    }
};
