<?php

namespace App\Support\Portfolio;

/**
 * Every editable single-value text/image on the public portfolio.
 *
 * The admin "Konten Halaman" screen is rendered from this list and the
 * seeder writes its defaults, so adding a field here is all it takes to make
 * a new piece of copy editable.
 *
 * Field types: text, textarea, url, email, color, toggle, image, file, select.
 */
class ContentSchema
{
    public static function groups(): array
    {
        return [
            'brand' => [
                'label' => 'Brand & Navbar',
                'icon' => 'ki-abstract-26',
                'fields' => [
                    'brand_name' => ['Nama brand / logo teks', 'text', 'RENDY IRAWAN'],
                    'brand_logo' => ['Logo (kosongkan untuk logo teks)', 'image', null],
                    'nav_cta_label' => ['Tombol navbar — teks', 'text', 'Hire Me'],
                    'nav_cta_link' => ['Tombol navbar — link', 'text', '#contact'],
                ],
            ],
            'hero' => [
                'label' => 'Hero',
                'icon' => 'ki-rocket',
                'fields' => [
                    'hero_eyebrow' => ['Teks kecil di atas judul', 'text', 'Found Best Developer'],
                    'hero_title_1' => ['Judul baris 1 (putih)', 'text', 'From Idea'],
                    'hero_title_2' => ['Judul baris 2 (abu/gradasi)', 'text', 'To Impact'],
                    'hero_subtitle' => ['Subjudul', 'textarea', 'Building products at the intersection of AI, creativity, and technology.'],
                    'hero_highlight' => ['Kata yang disorot di subjudul (pisahkan koma)', 'text', 'creativity, technology'],
                    'hero_description' => ['Deskripsi singkat', 'textarea', 'I turn bold ideas into intelligent, intuitive products that drive real-world impact.'],
                    'hero_cta_label' => ['Tombol utama — teks', 'text', "Let's build something"],
                    'hero_cta_link' => ['Tombol utama — link', 'text', '#contact'],
                    'hero_cta2_label' => ['Tombol kedua — teks', 'text', 'View Projects'],
                    'hero_cta2_link' => ['Tombol kedua — link', 'text', '#projects'],
                    'hero_image' => ['Gambar hero (PNG transparan / foto)', 'image', null],
                    'hero_badge' => ['Badge lingkaran', 'text', 'Trusted by innovators worldwide'],
                    'hero_card1_title' => ['Kartu 1 — judul', 'text', 'Latest Work'],
                    'hero_card1_text' => ['Kartu 1 — teks', 'text', 'Explore case studies & shipped products.'],
                    'hero_card1_label' => ['Kartu 1 — tombol', 'text', 'Open'],
                    'hero_card1_link' => ['Kartu 1 — link', 'text', '#projects'],
                    'hero_card2_title' => ['Kartu 2 — judul', 'text', 'Hire Me'],
                    'hero_card2_text' => ['Kartu 2 — teks', 'text', 'Available for freelance & contracts.'],
                    'hero_card2_label' => ['Kartu 2 — tombol', 'text', 'Contact'],
                    'hero_card2_link' => ['Kartu 2 — link', 'text', '#contact'],
                ],
            ],
            'about' => [
                'label' => 'Profil / Tentang',
                'icon' => 'ki-profile-circle',
                'fields' => [
                    'about_title' => ['Judul seksi', 'text', 'About Me'],
                    'about_name' => ['Nama lengkap', 'text', 'Rendy Irawan'],
                    'about_role' => ['Profesi', 'text', 'Full-Stack Developer & AI Engineer'],
                    'about_location' => ['Lokasi', 'text', 'Jakarta, ID'],
                    'about_timezone' => ['Zona waktu (untuk jam live)', 'text', 'Asia/Jakarta'],
                    'about_available' => ['Tampilkan status "Ready for work"', 'toggle', '1'],
                    'about_available_text' => ['Teks status', 'text', 'Ready for work'],
                    'about_photo' => ['Foto profil', 'image', null],
                    'about_bio' => ['Bio', 'textarea', "I'm a developer who loves crafting fast, secure and beautiful web products. From idea to launch, I help young and scaling businesses build strong digital solutions."],
                    'about_level' => ['Level (HUD, mis. tahun pengalaman)', 'text', '07'],
                    'about_level_label' => ['Label level', 'text', 'Years XP'],
                    'about_resume' => ['CV / Resume (PDF)', 'file', null],
                    'about_resume_label' => ['Teks tombol CV', 'text', 'Download CV'],
                ],
            ],
            'sections' => [
                'label' => 'Judul Tiap Bagian',
                'icon' => 'ki-text-align-left',
                'fields' => [
                    'services_title' => ['Layanan — judul', 'text', 'What I Do'],
                    'services_subtitle' => ['Layanan — subjudul', 'text', 'Services crafted to move your product forward.'],
                    'skills_title' => ['Keahlian — judul', 'text', 'Skill Tree'],
                    'skills_subtitle' => ['Keahlian — subjudul', 'text', 'Tools and technologies I use every day.'],
                    'projects_title' => ['Project — judul', 'text', 'Selected Work'],
                    'projects_subtitle' => ['Project — subjudul', 'text', 'Projects & products I have designed and shipped.'],
                    'experience_title' => ['Pengalaman — judul', 'text', 'Quest Log'],
                    'experience_subtitle' => ['Pengalaman — subjudul', 'text', 'Where I have worked and what I achieved.'],
                    'testimonials_title' => ['Testimoni — judul', 'text', 'Client Reviews'],
                    'testimonials_subtitle' => ['Testimoni — subjudul', 'text', 'What people say about working with me.'],
                ],
            ],
            'contact' => [
                'label' => 'Kontak',
                'icon' => 'ki-sms',
                'fields' => [
                    'contact_title' => ['Judul', 'text', "Let's Work Together"],
                    'contact_subtitle' => ['Subjudul', 'textarea', 'Have a project in mind? Tell me about it and I will reply within 24 hours.'],
                    'contact_email' => ['Email tujuan (pesan form dikirim ke sini)', 'email', 'rendy9008@gmail.com'],
                    'contact_whatsapp' => ['Nomor WhatsApp (format 628xxx)', 'text', '628123456789'],
                    'contact_whatsapp_text' => ['Pesan awal WhatsApp', 'text', 'Halo, saya tertarik untuk membuat project bersama Anda.'],
                    'contact_linkedin' => ['URL LinkedIn', 'url', 'https://linkedin.com/in/rendyirawann'],
                    'contact_address' => ['Alamat / area kerja', 'text', 'Jakarta, Indonesia — Remote worldwide'],
                    'contact_form_enabled' => ['Aktifkan form kontak', 'toggle', '1'],
                    'contact_budgets' => ['Pilihan budget (pisahkan koma)', 'text', '< $500, $500 – $2k, $2k – $5k, > $5k'],
                ],
            ],
            'footer' => [
                'label' => 'Footer',
                'icon' => 'ki-row-horizontal',
                'fields' => [
                    'footer_cta_title' => ['Ajakan di footer', 'text', 'Got an idea? Let\'s make it real.'],
                    'footer_about' => ['Teks singkat footer', 'textarea', 'Designing and building digital products with a focus on speed, security and delight.'],
                    'footer_nav_title' => ['Judul kolom navigasi', 'text', 'Navigasi'],
                    'footer_contact_title' => ['Judul kolom kontak', 'text', 'Kontak'],
                    'footer_copyright' => ['Nama di copyright (tahun otomatis)', 'text', 'Rendy Irawan'],
                    'footer_show_socials' => ['Tampilkan ikon sosial media di footer', 'toggle', '1'],
                ],
            ],
            'login' => [
                'label' => 'Halaman Login & Loader',
                'icon' => 'ki-lock-2',
                'fields' => [
                    'login_brand_name' => ['Nama (panel kiri, loader & copyright)', 'text', 'Dormansyah Pasaribu'],
                    'login_tagline' => ['Tagline di bawah nama', 'text', 'Portfolio Control Center'],
                    'login_kicker' => ['Teks kecil di atas judul', 'text', 'Control Center'],
                    'login_headline_1' => ['Judul baris 1 (putih)', 'text', 'Build the'],
                    'login_headline_2' => ['Judul baris 2 (abu)', 'text', 'Portfolio'],
                    'login_lead' => ['Deskripsi', 'textarea', 'Panel admin portfolio — kelola konten, project, dan pesan klien.'],
                    'login_point_1' => ['Poin 1', 'text', 'Kelola hero, profil, layanan & semua konten website'],
                    'login_point_2' => ['Poin 2', 'text', 'Upload project & produk — tempel gambar langsung'],
                    'login_point_3' => ['Poin 3', 'text', 'Pesan klien masuk langsung ke inbox & email'],
                    'login_card_title' => ['Judul form', 'text', 'Selamat datang kembali'],
                    'login_card_subtitle' => ['Subjudul form', 'text', 'Masuk untuk melanjutkan ke dashboard.'],
                    'login_button' => ['Teks tombol', 'text', 'Masuk'],
                    'login_button_busy' => ['Teks tombol saat memproses', 'text', 'Memverifikasi...'],
                    'login_foot' => ['Teks di bawah form', 'text', 'Butuh akses? Hubungi administrator sistem Anda.'],
                    'login_loader_label' => ['Loader — nama (kosongkan = nama di atas)', 'text', ''],
                    'login_loader_text' => ['Loader — teks di bawah bar', 'text', 'Memuat aplikasi…'],
                    'login_progress_1' => ['Proses masuk — langkah 1', 'text', 'Memverifikasi kredensial'],
                    'login_progress_2' => ['Proses masuk — langkah 2', 'text', 'Kredensial terverifikasi'],
                    'login_progress_3' => ['Proses masuk — langkah 3', 'text', 'Menyiapkan sesi aman'],
                    'login_progress_4' => ['Proses masuk — langkah 4', 'text', 'Membuka dashboard'],
                ],
            ],
            'admin' => [
                'label' => 'Navbar & Footer Admin',
                'icon' => 'ki-element-plus',
                'fields' => [
                    'admin_brand_name' => ['Nama di navbar admin', 'text', 'Rendy Irawan'],
                    'admin_brand_tagline' => ['Tagline di navbar admin', 'text', 'Portfolio Control Center'],
                    'admin_logo' => ['Logo admin (kosongkan = logo utama)', 'image', null],
                    'admin_footer_text' => ['Nama di copyright footer admin (tahun otomatis)', 'text', 'Rendy Irawan'],
                    'admin_footer_link' => ['Link saat nama di footer diklik', 'url', ''],
                    'admin_footer_show_socials' => ['Tampilkan ikon sosial media (dari menu Sosial Media)', 'toggle', '1'],
                ],
            ],
            'seo' => [
                'label' => 'SEO',
                'icon' => 'ki-search-list',
                'fields' => [
                    'seo_title' => ['Meta title', 'text', 'Dormansyah Pasaribu — Portfolio'],
                    'seo_description' => ['Meta description (maks 160 karakter)', 'textarea', 'Portfolio Dormansyah Pasaribu: lihat project, produk, pengalaman, dan layanan yang pernah dikerjakan.'],
                    'seo_keywords' => ['Keywords', 'text', 'dormansyah pasaribu, portfolio, project manager, project, produk'],
                    'seo_og_image' => ['Gambar share (1200×630)', 'image', null],
                    'seo_robots' => ['Robots', 'select', 'index, follow', ['index, follow', 'noindex, nofollow', 'index, nofollow', 'noindex, follow']],
                    'seo_theme_color' => ['Warna tema browser', 'color', '#07070d'],
                    'seo_google_verification' => ['Google site verification', 'text', ''],
                ],
            ],
        ];
    }

    /** @return array<string, array{0:string,1:string,2:mixed,3?:array}> flat key => definition */
    public static function fields(): array
    {
        return array_merge(...array_values(array_map(fn ($g) => $g['fields'], self::groups())));
    }

    /** @return array<string,mixed> key => default value */
    public static function defaults(): array
    {
        return array_map(fn ($f) => $f[2], self::fields());
    }
}
