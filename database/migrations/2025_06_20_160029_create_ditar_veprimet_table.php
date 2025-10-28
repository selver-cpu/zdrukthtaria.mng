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
        Schema::create('ditar_veprimet', function (Blueprint $table) {
            $table->id('ditar_id');
            $table->foreignId('perdorues_id')->nullable()->constrained('perdoruesit', 'perdorues_id')->onDelete('set null');
            $table->text('veprimi');
            $table->integer('objekt_id')->nullable();
            $table->string('objekt_tipi', 50)->nullable();
            $table->ipAddress('ip_adresa')->nullable();
            $table->timestamp('data_veprimit')->useCurrent();

            // Index
            $table->index('perdorues_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ditar_veprimet');
    }
};
