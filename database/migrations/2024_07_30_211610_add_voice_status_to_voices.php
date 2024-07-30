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
        Schema::table('voices', function (Blueprint $table) {
            $table->boolean('status')->default(0)->comment('0 => failure, 1 => success')->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('voices', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
