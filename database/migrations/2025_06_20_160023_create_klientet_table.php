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
        Schema::create('klientet', function (Blueprint $table) {
            $table->id('klient_id');
            $table->string('person_kontakti', 255);
            $table->string('telefon_kontakt', 20)->nullable();
            $table->string('email_kontakt', 255)->nullable();
            $table->text('adresa_faktura')->nullable();
            $table->string('qyteti', 100)->nullable();
            $table->string('kodi_postal', 20)->nullable();
            $table->string('shteti', 100)->nullable();
            $table->text('shenime')->nullable();
            $table->timestamp('data_krijimit')->useCurrent();
            $table->timestamp('data_perditesimit')->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('klientet');
    }
};
