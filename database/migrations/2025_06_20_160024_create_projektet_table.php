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
        Schema::create('projektet', function (Blueprint $table) {
            $table->id('projekt_id');
            $table->foreignId('klient_id')->constrained('klientet', 'klient_id')->onDelete('restrict');
            $table->string('emri_projektit');
            $table->text('pershkrimi')->nullable();
            $table->date('data_fillimit_parashikuar')->nullable();
            $table->date('data_perfundimit_parashikuar')->nullable();
            $table->date('data_perfundimit_real')->nullable();
            $table->foreignId('status_id')->constrained('statuset_projektit', 'status_id')->onDelete('restrict');
            $table->foreignId('mjeshtri_caktuar_id')->nullable()->constrained('perdoruesit', 'perdorues_id')->onDelete('set null');
            $table->foreignId('montuesi_caktuar_id')->nullable()->constrained('perdoruesit', 'perdorues_id')->onDelete('set null');
            $table->text('shenime_projekt')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('klient_id');
            $table->index('status_id');
            $table->index('mjeshtri_caktuar_id');
            $table->index('montuesi_caktuar_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projektet');
    }
};
