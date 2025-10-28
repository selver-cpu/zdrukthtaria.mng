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
        Schema::create('projektet_dimensions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('projekt_id')->constrained('projektet', 'projekt_id')->onDelete('cascade');
            $table->string('emri_pjeses'); // Emri i pjesës p.sh. "Panel anësor"
            $table->decimal('gjatesia', 8, 2); // Në mm
            $table->decimal('gjeresia', 8, 2); // Në mm
            $table->decimal('trashesia', 8, 2); // Në mm
            $table->enum('njesi_matese', ['mm', 'cm', 'm'])->default('mm');
            $table->integer('sasia')->default(1); // Numri i pjesëve identike
            $table->foreignId('materiali_id')->nullable()->constrained('materialet', 'material_id')->onDelete('set null');
            $table->string('materiali_personal')->nullable(); // Nëse nuk ekziston në bazën e të dhënave

            // Kantimi - Edge Banding
            $table->boolean('kantim_needed')->default(false);
            $table->enum('kantim_type', ['PVC', 'ABS', 'Wood Veneer', 'Aluminum'])->nullable();
            $table->decimal('kantim_thickness', 5, 2)->nullable(); // Trashësia e kantimit

            // Specifikimi i anëve për kantim
            $table->boolean('kantim_front')->default(false); // Ana e përparme
            $table->boolean('kantim_back')->default(false);  // Ana e pasme
            $table->boolean('kantim_left')->default(false);  // Ana e majtë
            $table->boolean('kantim_right')->default(false); // Ana e djathtë

            // Qoshet
            $table->enum('kantim_corners', ['square', 'rounded'])->default('square');

            // Gjurmimi
            $table->string('barcode')->unique()->nullable();
            $table->string('qr_code')->nullable();
            $table->enum('statusi_prodhimit', ['pending', 'cutting', 'edge_banding', 'completed'])->default('pending');
            $table->string('workstation_current')->nullable();
            $table->boolean('plc_ticket_printed')->default(false);
            $table->text('pershkrimi')->nullable(); // Udhëzime shtesë
            $table->foreignId('krijues_id')->constrained('perdoruesit', 'perdorues_id')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projektet_dimensions');
    }
};
