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
        Schema::table('dokumentet_projekti', function (Blueprint $table) {
            $table->string('emri_skedarit', 255)->change();
            $table->string('rruga_skedarit', 255)->change();
            $table->string('lloji_skedarit', 255)->change();
            $table->string('kategoria', 100)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dokumentet_projekti', function (Blueprint $table) {
            $table->string('emri_skedarit', 50)->change();
            $table->string('rruga_skedarit', 100)->change();
            $table->string('lloji_skedarit', 50)->change();
            $table->string('kategoria', 50)->change();
        });
    }
};
