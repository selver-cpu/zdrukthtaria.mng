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
        Schema::create('projekt_faza_pune', function (Blueprint $table) {
            $table->id();
            $table->foreignId('projekt_id')->constrained('projektet', 'projekt_id')->onDelete('cascade');
            $table->foreignId('faza_id')->constrained('fazat_projekti', 'id')->onDelete('restrict');
            $table->date('data_fillimit')->nullable();
            $table->date('data_perfundimit')->nullable();
            $table->text('komente')->nullable();
            $table->string('statusi_fazes')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('projekt_id');
            $table->index('faza_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projekt_faza_pune');
    }
};
