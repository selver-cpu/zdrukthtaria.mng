<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('makinat', function (Blueprint $table) {
            $table->id('makin_id');
            $table->string('emri', 255);
            $table->string('lloji', 50); // cnc, panel_saw, edge_bander, dust_collector, sander, other
            $table->string('marka', 100)->nullable();
            $table->string('modeli', 100)->nullable();
            $table->string('numri_serik', 100)->nullable();
            $table->string('lokacioni', 255)->nullable();
            $table->string('statusi', 30)->default('aktive'); // aktive, ne_mirembajtje, jashte_funksioni
            $table->string('kritikaliteti', 1)->default('B'); // A, B, C
            $table->date('data_blerjes')->nullable();
            $table->decimal('ore_pune', 12, 1)->default(0); // hour meter
            $table->text('shenime')->nullable();
            $table->timestamp('data_krijimit')->useCurrent();
            $table->timestamp('data_perditesimit')->useCurrent()->useCurrentOnUpdate();

            $table->index('lloji');
            $table->index('statusi');
            $table->index('kritikaliteti');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('makinat');
    }
};
