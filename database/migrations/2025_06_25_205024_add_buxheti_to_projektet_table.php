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
        Schema::table('projektet', function (Blueprint $table) {
            $table->decimal('buxheti', 15, 2)->default(0)->after('emri_projektit')->comment('Vetëm për administratorët');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projektet', function (Blueprint $table) {
            $table->dropColumn('buxheti');
        });
    }
};
