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
        Schema::create('dokumentet_projekti', function (Blueprint $table) {
            $table->id('dokument_id');
            $table->foreignId('projekt_id')->constrained('projektet', 'projekt_id')->onDelete('cascade');
            $table->string('emri_skedarit', 255);
            $table->string('lloji_skedarit', 50);
            $table->text('rruga_skedarit');
            $table->bigInteger('madhesia_skedarit')->nullable();
            $table->timestamp('data_ngarkimit')->useCurrent();
            $table->foreignId('perdorues_id_ngarkues')->nullable()->constrained('perdoruesit', 'perdorues_id')->onDelete('set null');
            $table->text('pershkrimi')->nullable();

            // Indexes
            $table->index('projekt_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokumentet_projekti');
    }
};
