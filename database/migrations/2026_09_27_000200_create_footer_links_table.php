<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/** Items of the website footer's "Navigasi" and "Kontak" columns. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('footer_links', function (Blueprint $table) {
            $table->id();
            $table->string('column', 20)->default('nav'); // nav | contact
            $table->string('label', 120);
            $table->string('url', 300)->nullable();
            $table->string('icon', 60)->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['column', 'is_active', 'sort_order']);
        });

        $now = now();
        $rows = [
            ['nav', 'Home', '#home'], ['nav', 'About', '#about'], ['nav', 'Services', '#services'],
            ['nav', 'Projects', '#projects'], ['nav', 'Experience', '#experience'], ['nav', 'Contact', '#contact'],
            ['nav', 'Semua Project', '/projects'],
            ['contact', 'rendy9008@gmail.com', 'mailto:rendy9008@gmail.com'],
            ['contact', '+628123456789', 'https://wa.me/628123456789'],
            ['contact', 'Jakarta, Indonesia — Remote worldwide', null],
        ];
        foreach ($rows as $i => [$column, $label, $url]) {
            DB::table('footer_links')->insert([
                'column' => $column, 'label' => $label, 'url' => $url, 'icon' => null,
                'sort_order' => $i + 1, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('footer_links');
    }
};
