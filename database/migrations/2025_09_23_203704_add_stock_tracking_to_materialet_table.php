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
        Schema::table('materialet', function (Blueprint $table) {
            $table->decimal('sasia_stokut', 12, 2)->default(0)->after('pershkrimi'); // Sasia në stok
            $table->decimal('sasia_minimale', 12, 2)->default(0)->after('sasia_stokut'); // Sasia minimale për alert
            $table->decimal('sasia_rezervuar', 12, 2)->default(0)->after('sasia_minimale'); // Sasia e rezervuar për projekte
            $table->decimal('cmimi_per_njesi', 10, 2)->nullable()->after('sasia_rezervuar'); // Çmimi për njësi
            $table->string('lokacioni')->nullable()->after('cmimi_per_njesi'); // Lokacioni në magazinë
            $table->boolean('alert_low_stock')->default(false)->after('lokacioni'); // Alert për stok të ulët
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('materialet', function (Blueprint $table) {
            $table->dropColumn([
                'sasia_stokut',
                'sasia_minimale',
                'sasia_rezervuar',
                'cmimi_per_njesi',
                'lokacioni',
                'alert_low_stock'
            ]);
        });
    }
};
