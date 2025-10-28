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
        Schema::table('statuset_projektit', function (Blueprint $table) {
            $table->string('klasa_css')->default('secondary')->after('renditja');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('statuset_projektit', function (Blueprint $table) {
            $table->dropColumn('klasa_css');
        });
    }
};
