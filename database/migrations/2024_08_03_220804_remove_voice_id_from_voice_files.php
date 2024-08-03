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
        Schema::table('voice_files', function (Blueprint $table) {
            $table->dropColumn('voice_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('voice_files', function (Blueprint $table) {
            $table->unsignedBigInteger('voice_id')->nullable()->after('id');
        });
    }
};
