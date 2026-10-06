<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mirembajtje_planet', function (Blueprint $table) {
            $table->id('plan_id');
            $table->foreignId('makin_id')->constrained('makinat', 'makin_id')->onDelete('cascade');
            $table->string('titulli', 255);
            $table->text('checklist')->nullable(); // steps, one per line
            $table->string('frekuenca', 20); // dite, jave, muaj, tremujor, vit, ore
            $table->unsignedInteger('intervali')->default(1); // every N days/weeks/hours
            $table->date('data_fundit_kryer')->nullable();
            $table->date('data_radhese')->nullable();
            $table->decimal('ore_fundit_kryer', 12, 1)->nullable();
            $table->boolean('aktiv')->default(true);
            $table->timestamp('data_krijimit')->useCurrent();
            $table->timestamp('data_perditesimit')->useCurrent()->useCurrentOnUpdate();

            $table->index(['makin_id', 'aktiv']);
            $table->index('data_radhese');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mirembajtje_planet');
    }
};
