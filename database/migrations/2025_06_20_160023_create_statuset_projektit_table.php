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
        Schema::create('statuset_projektit', function (Blueprint $table) {
            $table->id('status_id');
            $table->string('emri_statusit', 50)->unique();
            $table->text('pershkrimi')->nullable();
            $table->integer('renditja')->unique();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('statuset_projektit');
    }
};
