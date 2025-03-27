<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('tbl_leads', function (Blueprint $table) {
            // Drop existing foreign key (if any)
            $table->dropForeign(['lead_status']);

            // Ensure the column type matches the referenced table
            $table->unsignedBigInteger('lead_status')->nullable()->change();

            // Re-add the foreign key but name it as "tbl_leads_lead_status_foreign" while referencing `tbl_stage`
            $table->foreign('lead_status', 'tbl_leads_lead_status_foreign')
                ->references('id')
                ->on('tbl_lead_status')
                ->onDelete('set null')
                ->onUpdate('restrict');
        });
    }

    public function down()
    {
        Schema::table('tbl_leads', function (Blueprint $table) {
            // Drop the foreign key
            $table->dropForeign('tbl_leads_lead_status_foreign');

            // Re-add the original foreign key (modify as needed)
            $table->foreign('lead_status')
                ->references('id')
                ->on('tbl_stage');
        });
    }
};
