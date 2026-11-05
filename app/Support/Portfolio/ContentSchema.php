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
                    'brand_name' => ['Nama brand / logo teks', 'text', 'DORMANSYAH PASARIBU'],
                    'brand_logo' => ['Logo (kosongkan untuk logo teks)', 'image', null],
                    'nav_cta_label' => ['Tombol navbar — teks', 'text', 'Hire Me'],
                    'nav_cta_link' => ['Tombol navbar — link', 'text', '#contact'],
                ],
            ],
            'hero' => [
                'label' => 'Hero',
                'icon' => 'ki-rocket',
                'fields' => [
                    'hero_word' => ['Kata raksasa di latar hero (pisahkan koma untuk bergantian)', 'text', 'Portfolio, Delivery, Leadership'],
                    'hero_eyebrow' => ['Teks kecil di atas nama', 'text', "Hello, I'm"],
                    'hero_title_1' => ['Nama — baris 1', 'text', 'Dormansyah'],
                    'hero_title_2' => ['Nama — baris 2', 'text', 'Pasaribu'],
                    'hero_subtitle' => ['Tagline di kolom kanan (1 kalimat pendek)', 'textarea', 'Turning requirements into shipped software, on scope and on time.'],
                    'hero_highlight' => ['Kata yang disorot di tagline (pisahkan koma)', 'text', 'shipped software'],
                    'hero_description' => ['Deskripsi singkat', 'textarea', 'I lead cross-functional teams from discovery to release, keeping clients informed and delivery predictable.'],
                    'hero_cta_label' => ['Tombol utama — teks', 'text', "Let's talk"],
                    'hero_cta_link' => ['Tombol utama — link', 'text', '#contact'],
                    'hero_cta2_label' => ['Tombol kedua — teks', 'text', 'View work'],
                    'hero_cta2_link' => ['Tombol kedua — link', 'text', '#projects'],
                    'hero_image' => ['Gambar hero (PNG transparan / foto)', 'image', null],
                    'hero_deck_label' => ['Deck kartu project — label', 'text', 'Selected work'],
                    'hero_deck_text' => ['Deck kartu project — teks pendek', 'text', 'A few projects I have managed end-to-end.'],
                ],
            ],
            'mascot' => [
                'label' => 'Maskot Robot',
                'icon' => 'ki-abstract-41',
                'fields' => [
                    'mascot_enabled' => ['Tampilkan maskot robot melayang', 'toggle', '1'],
                    'mascot_badge1' => ['Badge 1 — judul (ke #projects)', 'text', 'Selected work'],
                    'mascot_badge1_sub' => ['Badge 1 — keterangan', 'text', 'see the projects'],
                    'mascot_badge2' => ['Badge 2 — judul (ke #experience)', 'text', 'Delivery track'],
                    'mascot_badge2_sub' => ['Badge 2 — keterangan', 'text', 'on scope, on time'],
                    'mascot_badge3' => ['Badge 3 — judul (ke #contact)', 'text', 'Start a project'],
                    'mascot_badge3_sub' => ['Badge 3 — keterangan', 'text', 'reply within 24h'],
                ],
            ],
            'about' => [
                'label' => 'Profil / Tentang',
                'icon' => 'ki-profile-circle',
                'fields' => [
                    'about_title' => ['Judul seksi', 'text', 'About Me'],
                    'about_name' => ['Nama lengkap', 'text', 'Dormansyah Pasaribu'],
                    'about_role' => ['Profesi', 'text', 'Full-Stack Developer & AI Engineer'],
                    'about_location' => ['Lokasi', 'text', 'Jakarta, ID'],
                    'about_timezone' => ['Zona waktu (untuk jam live)', 'text', 'Asia/Jakarta'],
                    'about_available' => ['Tampilkan status "Ready for work"', 'toggle', '1'],
                    'about_available_text' => ['Teks status', 'text', 'Ready for work'],
                    'about_photo' => ['Foto profil', 'image', null],
                    'about_bio' => ['Bio', 'textarea', "I'm a developer who loves crafting fast, secure and beautiful web products. From idea to launch, I help young and scaling businesses build strong digital solutions."],
                    'about_level' => ['Angka besar di bawah bio (mis. tahun pengalaman)', 'text', '07'],
                    'about_level_label' => ['Keterangan angka', 'text', 'Years of experience'],
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
                    'contact_email' => ['Email tujuan (pesan form dikirim ke sini)', 'email', 'dormansyah.pasaribu@gmail.com'],
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
                    'footer_cta_title' => ['Judul besar di tengah footer', 'text', 'Got an idea? Let\'s make it real.'],
                    'footer_about' => ['Kalimat kecil di atas judul footer', 'textarea', 'Designing and building digital products with a focus on speed, security and delight.'],
                    'footer_nav_title' => ['Judul kolom navigasi', 'text', 'Navigasi'],
                    'footer_contact_title' => ['Judul kolom kontak', 'text', 'Kontak'],
                    'footer_copyright' => ['Nama di copyright (tahun otomatis)', 'text', 'Dormansyah Pasaribu'],
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
                    'admin_brand_name' => ['Nama di navbar admin', 'text', 'Dormansyah Pasaribu'],
                    'admin_brand_tagline' => ['Tagline di navbar admin', 'text', 'Portfolio Control Center'],
                    'admin_logo' => ['Logo admin (kosongkan = logo utama)', 'image', null],
                    'admin_footer_text' => ['Nama di copyright footer admin (tahun otomatis)', 'text', 'Dormansyah Pasaribu'],
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
                    'seo_theme_color' => ['Warna tema browser', 'color', '#0d0d0f'],
                    'seo_google_verification' => ['Google site verification', 'text', ''],
                ],
            ],
        ];
    }

    /** Short guide shown at the top of each tab: what it controls and where it appears. */
    public static function guides(): array
    {
        return [
            'brand' => 'Mengatur logo/nama di pil navbar melayang, logo di layar loading dan footer, serta tombol putih di navbar. Pil tampil pendek di puncak halaman dan memanjang (menampilkan menu) setelah digulir. Logo bulat paling bagus.',
            'hero' => 'Bagian paling atas website. Kata raksasa merah di latar, foto di depannya, nama & profesi di kiri, tagline & statistik di kanan. Deck kartu di bawahnya otomatis mengambil 5 project pilihan (yang ditandai Featured lebih dulu).',
            'about' => 'Seksi "About Me": pita video sinematik di atas (mulai diputar saat digulir ke seksi ini), lalu foto, nama, profesi, bio, lokasi & status. Bio menyala kata demi kata saat digulir.',
            'mascot' => 'Robot kecil melayang di kanan bawah dengan tiga badge kaca yang bisa diklik. Pengunjung bisa menutupnya; pilihan itu diingat di browsernya.',
            'sections' => 'Judul & subjudul besar di atas setiap bagian halaman depan (Layanan, Keahlian, Project, Pengalaman, Testimoni). Isi daftarnya sendiri di menu Portfolio lainnya.',
            'contact' => 'Seksi kontak & form "Hubungi saya". Email tujuan menerima pesan dari form; tombol WhatsApp & LinkedIn membuka chat/profil langsung.',
            'footer' => 'Footer gelap bergradasi merah: logo, kalimat kecil, judul besar, tombol, lalu kolom tautan dan nama raksasa samar di dasar. Isi kolom Navigasi & Kontak dikelola di menu Portfolio › Link Footer; kolom Eksplorasi otomatis dari menu navbar.',
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
            'nav_cta_label' => 'Tombol putih di ujung kanan pil navbar. Contoh: Hire Me.',
            'nav_cta_link' => '#contact (ke seksi kontak), /projects, atau URL lengkap.',
            'hero_word' => 'Kata raksasa merah di belakang foto, diketik huruf per huruf. Pisahkan koma untuk beberapa kata bergantian, mis. Portfolio, Delivery, Leadership. Kata panjang otomatis mengecil.',
            'hero_eyebrow' => 'Teks kecil di atas nama. Contoh: Hello, I\'m.',
            'hero_title_1' => 'Nama depan / baris pertama nama besar.',
            'hero_title_2' => 'Nama belakang / baris kedua. Kosongkan jika nama satu kata.',
            'hero_subtitle' => 'Kalimat pendek di kolom kanan, di atas angka statistik. Profesi di bawah nama diambil dari tab Profil.',
            'hero_highlight' => 'Kata dari tagline yang diberi warna merah, pisahkan koma.',
            'hero_description' => 'Paragraf pendek di bawah profesi (opsional).',
            'hero_cta_link' => 'Tujuan tombol utama, biasanya #contact.',
            'hero_cta2_link' => 'Tujuan tombol kedua, biasanya #projects.',
            'hero_image' => 'Foto utama di kanan hero. Rasio potret (± 5:6); tepinya otomatis dipudarkan ke latar. Maks 10 MB.',
            'hero_deck_label' => 'Label kecil di kiri deck kartu project. Kosongkan jika tidak perlu.',
            'hero_deck_text' => 'Satu kalimat pendek di bawah label deck.',
            'about_title' => 'Label kecil merah di atas nama pada seksi profil. Contoh: About Me.',
            'about_name' => 'Nama besar di bawah foto profil & di panel data diri.',
            'about_role' => 'Profesi / jabatan. Contoh: Senior Project Manager.',
            'about_location' => 'Kota / negara, tampil di bar atas seksi profil.',
            'about_timezone' => 'Untuk jam live di sebelah lokasi. Contoh: Asia/Jakarta, Asia/Makassar.',
            'about_available_text' => 'Status di pill hijau. Contoh: Open to work.',
            'about_photo' => 'Foto potret rasio 4:5. Ditampilkan sedikit monokrom agar selaras dengan tema.',
            'about_bio' => '2–4 kalimat tentang pengalaman & nilai yang Anda tawarkan.',
            'about_level' => 'Angka besar di bawah bio, mis. lama pengalaman: 07. Kosongkan untuk menyembunyikan.',
            'about_level_label' => 'Keterangan angka. Contoh: Years of experience.',
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
            'footer_cta_title' => 'Judul besar di tengah footer, di bawah logo. Tombolnya memakai teks tombol utama hero.',
            'footer_about' => 'Kalimat pendek di antara logo dan judul footer.',
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
