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
        Schema::table('ditar_veprimet', function (Blueprint $table) {
            $table->json('te_dhenat_e_reja')->nullable()->after('ip_adresa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ditar_veprimet', function (Blueprint $table) {
            $table->dropColumn('te_dhenat_e_reja');
        });
    }
};
