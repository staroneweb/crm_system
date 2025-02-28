<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tbl_password_reset_tokens', function (Blueprint $table) {
            DB::statement('ALTER TABLE tbl_password_reset_tokens DROP PRIMARY KEY');
            $table->bigIncrements('id')->first(); // Ensures 'id' is the first column
            $table->unique('email');


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_password_reset_tokens', function (Blueprint $table) {
            //
        });
    }
};
