<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('outlets', function (Blueprint $table) {
            // Tambahkan kolom boolean, defaultnya true (menerima pesanan)
            $table->boolean('is_accepting_orders')->default(true)->after('image');
        });
    }

    public function down(): void
    {
        Schema::table('outlets', function (Blueprint $table) {
            $table->dropColumn('is_accepting_orders');
        });
    }
};
