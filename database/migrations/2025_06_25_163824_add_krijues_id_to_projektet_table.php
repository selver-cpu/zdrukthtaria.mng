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
        // Fillimisht shtoj kolonën si nullable
        Schema::table('projektet', function (Blueprint $table) {
            $table->unsignedBigInteger('krijues_id')->nullable()->after('projekt_id');
        });
        
        // Vendos vlerën 1 (administrator) për të gjitha projektet ekzistuese
        \Illuminate\Support\Facades\DB::table('projektet')
            ->whereNull('krijues_id')
            ->update(['krijues_id' => 1]);
            
        // Tani shtoj foreign key constraint dhe bëj kolonën NOT NULL
        Schema::table('projektet', function (Blueprint $table) {
            $table->unsignedBigInteger('krijues_id')->nullable(false)->change();
            $table->foreign('krijues_id')->references('perdorues_id')->on('perdoruesit')->onDelete('restrict');
            $table->index('krijues_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projektet', function (Blueprint $table) {
            // Heq indeksin dhe foreign key constraint
            $table->dropForeign(['krijues_id']);
            $table->dropIndex(['krijues_id']);
            $table->dropColumn('krijues_id');
        });
    }
};
