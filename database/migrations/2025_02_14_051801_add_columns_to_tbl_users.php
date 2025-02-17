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
        Schema::table('tbl_users', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id')->nullable()->after('id');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('set null');

            $table->string('last_name', 100)->after('name');

            $table->string('profile_image', 255)->nullable()->after('email');
            $table->tinyInteger('status')->default(1)->comment('1 for active, 0 for inactive')->after('profile_image');
            $table->timestamp('last_login_at')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_users', function (Blueprint $table) {
            //
        });
    }
};
