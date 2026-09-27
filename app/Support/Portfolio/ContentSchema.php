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

    /** Short guide shown at the top of each tab: what it controls and where it appears. */
    public static function guides(): array
    {
        return [
            'brand' => 'Mengatur logo/nama di kiri atas website dan tombol ajakan di navbar. Isi logo jika punya file logo; jika kosong, website memakai logo ikon + nama brand.',
            'hero' => 'Bagian paling atas website — kesan pertama pengunjung. Buat judul singkat (2–3 kata per baris), subjudul 1 kalimat, lalu arahkan tombol ke bagian penting (#contact / #projects).',
            'about' => 'Seksi "About Me": foto, nama, profesi, bio, dan panel data diri ala HUD game. Gunakan foto PNG tanpa background agar menyatu dengan efek cahaya.',
            'sections' => 'Judul & subjudul besar di atas setiap bagian halaman depan (Layanan, Keahlian, Project, Pengalaman, Testimoni). Isi daftarnya sendiri di menu Portfolio lainnya.',
            'contact' => 'Seksi kontak & form "Hubungi saya". Email tujuan menerima pesan dari form; tombol WhatsApp & LinkedIn membuka chat/profil langsung.',
            'footer' => 'Bagian paling bawah website. Isi kolom Navigasi & Kontak dikelola di menu Portfolio › Link Footer.',
            'login' => 'Semua teks di halaman login admin, layar loading saat halaman dibuka, dan tahapan proses saat tombol Masuk ditekan.',
            'admin' => 'Nama, logo, dan footer di panel admin ini (bukan website). Ikon di footer admin diambil dari menu Sosial Media.',
            'seo' => 'Cara website tampil di Google & saat link dibagikan (WhatsApp, LinkedIn). Meta title ± 60 karakter, description ± 155 karakter, gambar share 1200×630 px.',
        ];
    }

    /** Hint shown under individual fields (examples, limits, where it appears). */
    public static function help(): array
    {
        return [
            'brand_name' => 'Tampil di navbar & footer website. Contoh: DORMANSYAH.',
            'brand_logo' => 'PNG/SVG transparan, tinggi ± 36 px saat tampil. Kosongkan untuk memakai ikon + nama.',
            'nav_cta_label' => 'Teks tombol di kanan navbar / menu. Contoh: Hire Me.',
            'nav_cta_link' => '#contact (ke seksi kontak), /projects, atau URL lengkap.',
            'hero_eyebrow' => 'Teks kecil di atas judul. Contoh: Project Manager.',
            'hero_title_1' => 'Baris pertama judul besar (putih). Singkat: 2–3 kata.',
            'hero_title_2' => 'Baris kedua judul (abu). Contoh: To Impact.',
            'hero_subtitle' => '1 kalimat yang menjelaskan apa yang Anda kerjakan.',
            'hero_highlight' => 'Kata dari subjudul yang diberi warna merah/biru, pisahkan koma. Contoh: strategi, delivery',
            'hero_description' => 'Kalimat pendukung di bawah subjudul (opsional).',
            'hero_cta_link' => 'Tujuan tombol utama, biasanya #contact.',
            'hero_cta2_link' => 'Tujuan tombol kedua, biasanya #projects.',
            'hero_image' => 'Foto utama di kanan hero. Rasio potret (± 5:6), PNG tanpa background paling bagus. Maks 10 MB.',
            'hero_badge' => 'Teks di sebelah lingkaran berputar. Kosongkan untuk menyembunyikan.',
            'hero_card1_title' => 'Kartu kecil di kanan bawah hero. Kosongkan judul untuk menyembunyikan kartu.',
            'hero_card2_title' => 'Kartu kedua. Kosongkan judul untuk menyembunyikan.',
            'about_title' => 'Label kecil di tengah atas seksi profil. Contoh: About Me.',
            'about_name' => 'Nama besar di bawah foto profil & di panel data diri.',
            'about_role' => 'Profesi / jabatan. Contoh: Senior Project Manager.',
            'about_location' => 'Kota / negara, tampil di bar atas seksi profil.',
            'about_timezone' => 'Untuk jam live di sebelah lokasi. Contoh: Asia/Jakarta, Asia/Makassar.',
            'about_available_text' => 'Status di pill hijau. Contoh: Open to work.',
            'about_photo' => 'Foto potret close-up, PNG tanpa background agar menyatu dengan cahaya spotlight.',
            'about_bio' => '2–4 kalimat tentang pengalaman & nilai yang Anda tawarkan.',
            'about_level' => 'Angka besar di panel HUD, mis. lama pengalaman: 07.',
            'about_level_label' => 'Keterangan angka level. Contoh: Years XP.',
            'about_resume' => 'File CV (PDF). Muncul sebagai tombol download di panel profil.',
            'services_title' => 'Judul seksi Layanan. Contoh: What I Do.',
            'skills_title' => 'Judul seksi Keahlian.',
            'projects_title' => 'Judul seksi Project.',
            'experience_title' => 'Judul seksi Pengalaman.',
            'testimonials_title' => 'Judul seksi Testimoni.',
            'contact_title' => 'Judul besar seksi kontak.',
            'contact_email' => 'Pesan dari form kontak dikirim ke email ini.',
            'contact_whatsapp' => 'Format internasional tanpa + dan spasi. Contoh: 6281234567890.',
            'contact_whatsapp_text' => 'Pesan yang otomatis terisi saat pengunjung membuka WhatsApp.',
            'contact_linkedin' => 'URL profil lengkap. Contoh: https://linkedin.com/in/nama-anda',
            'contact_budgets' => 'Pilihan dropdown "Budget" di form, pisahkan koma.',
            'contact_form_enabled' => 'Matikan jika hanya ingin tombol WhatsApp/LinkedIn/Email.',
            'footer_cta_title' => 'Kalimat ajakan besar di atas footer.',
            'footer_about' => 'Deskripsi singkat di bawah logo footer.',
            'footer_copyright' => 'Hanya nama. Tahun terisi otomatis: © 2026 Nama. All rights reserved.',
            'login_brand_name' => 'Nama di panel kiri halaman login, di layar loading, dan copyright.',
            'login_lead' => 'Kalimat di bawah judul besar halaman login.',
            'login_loader_text' => 'Teks kecil di bawah bar loading saat halaman login dibuka.',
            'login_progress_1' => 'Tahap proses setelah tombol Masuk ditekan (tampil berurutan).',
            'admin_logo' => 'Kosongkan untuk memakai logo utama aplikasi.',
            'admin_footer_link' => 'Opsional. Jika diisi, nama di footer admin bisa diklik.',
            'seo_title' => 'Judul di tab browser & hasil Google. ± 60 karakter.',
            'seo_description' => 'Ringkasan di hasil Google. ± 155 karakter, sebut nama & keahlian.',
            'seo_keywords' => 'Kata kunci dipisah koma.',
            'seo_og_image' => 'Gambar saat link dibagikan (WhatsApp/LinkedIn). Ukuran ideal 1200×630 px.',
            'seo_robots' => '"index, follow" = boleh muncul di Google. "noindex" untuk menyembunyikan.',
            'seo_google_verification' => 'Kode dari Google Search Console (opsional).',
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
