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
        Schema::table("dokumentet_projekti", function (Blueprint $table) {
            $table->string("kategoria", 50)->nullable()->after("pershkrimi");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("dokumentet_projekti", function (Blueprint $table) {
            $table->dropColumn("kategoria");
        });
    }
};
