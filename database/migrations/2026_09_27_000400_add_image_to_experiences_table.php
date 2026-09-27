<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Foto tempat kerja / instansi untuk tiap pengalaman.
 *
 * Kolomnya nullable: kalau tidak diisi, timeline tetap memakai belah ketupat
 * merah seperti sebelumnya. Kalau diisi, fotonya dipasang DI DALAM belah
 * ketupat merah itu (bingkainya dipertahankan) dan bisa diklik untuk membuka
 * modal detail.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('experiences', function (Blueprint $table) {
            $table->string('image')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('experiences', function (Blueprint $table) {
            $table->dropColumn('image');
        });
    }
};
