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
        Schema::create('perdoruesit', function (Blueprint $table) {
            $table->id('perdorues_id');
            $table->foreignId('rol_id')->constrained('rolet', 'rol_id')->onDelete('restrict');
            $table->string('emri', 100);
            $table->string('mbiemri', 100);
            $table->string('email', 255)->unique();
            $table->string('fjalekalimi_hash', 255);
            $table->string('telefon', 20)->nullable();
            $table->text('adresa')->nullable();
            $table->boolean('aktiv')->default(true);
            $table->timestamp('data_krijimit')->useCurrent();
            $table->timestamp('data_perditesimit')->useCurrent()->useCurrentOnUpdate();

            // Index
            $table->index('rol_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perdoruesit');
    }
};
