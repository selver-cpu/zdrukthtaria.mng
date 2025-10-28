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
        Schema::create('projekt_materiale', function (Blueprint $table) {
            $table->id('projekt_material_id');
            $table->foreignId('projekt_id')->constrained('projektet', 'projekt_id')->onDelete('cascade');
            $table->foreignId('material_id')->constrained('materialet', 'material_id')->onDelete('restrict');
            $table->decimal('sasia_perdorur', 10, 2);
            
            // Unique constraint për projekt_id dhe material_id
            $table->unique(['projekt_id', 'material_id']);

            // Indexes
            $table->index('projekt_id');
            $table->index('material_id');
        });
        // CHECK constraint removed for SQLite compatibility
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projekt_materiale');
    }
};
