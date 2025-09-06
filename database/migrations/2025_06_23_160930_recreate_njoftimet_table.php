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
        Schema::dropIfExists('njoftimet');
        
        Schema::create('njoftimet', function (Blueprint $table) {
            $table->id('njoftim_id');
            $table->foreignId('perdorues_id')->constrained('perdoruesit', 'perdorues_id')->onDelete('cascade');
            $table->foreignId('projekt_id')->nullable()->constrained('projektet', 'projekt_id')->onDelete('set null');
            $table->text('mesazhi');
            $table->string('lloji_njoftimit')->check("lloji_njoftimit IN ('email', 'sms', 'system')");
            $table->boolean('lexuar')->default(false);
            $table->timestamp('data_krijimit')->useCurrent();
            
            // Indekset sipas database_schema.sql
            $table->index('perdorues_id');
            $table->index('projekt_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('njoftimet');
    }
};
