<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mirembajtje_evidenca', function (Blueprint $table) {
            $table->id('evidenca_id');
            $table->foreignId('makin_id')->constrained('makinat', 'makin_id')->onDelete('cascade');
            $table->foreignId('plan_id')->nullable()->constrained('mirembajtje_planet', 'plan_id')->onDelete('set null');
            $table->string('lloji', 30)->default('preventive'); // preventive, corrective, inspection
            $table->string('titulli', 255);
            $table->text('pershkrimi')->nullable();
            $table->string('statusi', 30)->default('e_hapur'); // e_hapur, ne_progres, perfunduar
            $table->string('prioriteti', 20)->default('mesatar'); // ulet, mesatar, larte, urgjent
            $table->foreignId('perdorues_id')->nullable()->constrained('perdoruesit', 'perdorues_id')->onDelete('set null');
            $table->date('data_planifikuar')->nullable();
            $table->dateTime('data_kryerjes')->nullable();
            $table->unsignedInteger('kohezgjatja_minuta')->nullable();
            $table->decimal('kosto', 12, 2)->nullable();
            $table->text('pjeset_e_perdorura')->nullable();
            $table->text('gjetjet')->nullable();
            $table->timestamp('data_krijimit')->useCurrent();
            $table->timestamp('data_perditesimit')->useCurrent()->useCurrentOnUpdate();

            $table->index(['makin_id', 'statusi']);
            $table->index('lloji');
            $table->index('data_planifikuar');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mirembajtje_evidenca');
    }
};
