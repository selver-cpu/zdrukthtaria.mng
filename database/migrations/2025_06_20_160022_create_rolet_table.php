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
        Schema::create('rolet', function (Blueprint $table) {
            $table->id('rol_id');
            $table->enum('emri_rolit', ['administrator', 'menaxher', 'mjeshtër', 'montues'])->unique();
            $table->text('pershkrimi')->nullable();
            $table->timestamp('data_krijimit')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rolet');
    }
};
