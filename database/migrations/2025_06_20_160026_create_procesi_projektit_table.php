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
        Schema::create('procesi_projektit', function (Blueprint $table) {
            $table->id('proces_id');
            $table->foreignId('projekt_id')->constrained('projektet', 'projekt_id')->onDelete('cascade');
            $table->foreignId('status_id')->constrained('statuset_projektit', 'status_id')->onDelete('restrict');
            $table->foreignId('perdorues_id')->constrained('perdoruesit', 'perdorues_id')->onDelete('restrict');
            $table->timestamp('data_ndryshimit')->useCurrent();
            $table->text('komente')->nullable();

            // Indexes
            $table->index('projekt_id');
            $table->index('status_id');
            $table->index('perdorues_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('procesi_projektit');
    }
};
