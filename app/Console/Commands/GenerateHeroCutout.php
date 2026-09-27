<?php

namespace App\Console\Commands;

use App\Models\Portfolio\PageContent;
use Illuminate\Console\Command;

/**
 * Membuat versi transparan dari foto hero.
 *
 * Kenapa perlu command tersendiri, bukan sekadar CSS:
 * foto hero-nya sangat low-key — latarnya luminance 1-2, dahinya 8, pipinya 20.
 * Tidak ada ambang terang-gelap yang bisa memisahkan subjek dari latarnya,
 * sehingga mask maupun blend selalu berakhir salah satu: kotak latarnya muncul,
 * atau wajahnya ikut hilang. Pemisahan hanya mungkin dilakukan di level piksel.
 *
 * Caranya flood fill dari tepi gambar: latar menyambung ke pinggir, sedangkan
 * wajah terkurung garis rim light yang terang. Badan/jaketnya hitam murni dan
 * menyambung ke latar sehingga ikut terhapus — itu tidak bisa dihindari, maka
 * bagian dada ke bawah sekalian dipudarkan agar terlihat disengaja.
 *
 * Hasilnya ditulis ke folder uploads di samping berkas aslinya, lalu nilai
 * hero_image diarahkan ke situ, sehingga foto hero tetap satu sumber dan tetap
 * terkelola dari admin. Folder uploads hanya bisa ditulis PHP-FPM (ACL mask
 * r-x), jadi command ini dijalankan sebagai www-data.
 *
 * Berkas asli tidak pernah ditimpa; kalau hasilnya tidak disukai, nilai
 * hero_image tinggal dikembalikan ke berkas aslinya.
 *
 * Jalankan ulang setiap kali foto hero diganti dari admin.
 */
class GenerateHeroCutout extends Command
{
    protected $signature = 'portfolio:hero-cutout
                            {--threshold=9 : Ambang luminance yang dianggap latar}
                            {--fade-start=0.60 : Posisi mulai pudar (0-1 dari tinggi)}
                            {--fade-end=0.84 : Posisi pudar habis (0-1 dari tinggi)}
                            {--apply : Arahkan hero_image ke hasil cutout}';

    protected $description = 'Buat versi transparan foto hero (latar dihapus, dada ke bawah dipudarkan)';

    public function handle(): int
    {
        $value = PageContent::where('key', 'hero_image')->value('value');

        if (! $value) {
            $this->error('hero_image belum diisi.');

            return self::FAILURE;
        }

        $src = public_path($value);

        if (! is_file($src)) {
            $this->error("Berkas tidak ditemukan: {$src}");

            return self::FAILURE;
        }

        $im = @imagecreatefromstring(file_get_contents($src));

        if (! $im) {
            $this->error('Format gambar tidak dikenali.');

            return self::FAILURE;
        }

        $w = imagesx($im);
        $h = imagesy($im);
        $thr = (int) $this->option('threshold');

        $lum = new \SplFixedArray($w * $h);

        for ($y = 0; $y < $h; $y++) {
            for ($x = 0; $x < $w; $x++) {
                $c = imagecolorat($im, $x, $y);
                $lum[$y * $w + $x] = (int) round(
                    0.2126 * (($c >> 16) & 255) + 0.7152 * (($c >> 8) & 255) + 0.0722 * ($c & 255)
                );
            }
        }

        // Benih hanya dari tepi atas, kiri, dan kanan. Tepi bawah sengaja
        // dilewati: subjek menyentuh dasar gambar, kalau ikut dijadikan benih
        // maka flood fill masuk lewat bawah dan menghapus lebih banyak lagi.
        $bg = new \SplFixedArray($w * $h);

        for ($i = 0; $i < $w * $h; $i++) {
            $bg[$i] = 0;
        }

        $queue = new \SplQueue();

        $seed = function (int $x, int $y) use ($w, $lum, $bg, $queue, $thr): void {
            $i = $y * $w + $x;

            if (! $bg[$i] && $lum[$i] <= $thr) {
                $bg[$i] = 1;
                $queue->enqueue($i);
            }
        };

        for ($x = 0; $x < $w; $x++) {
            $seed($x, 0);
        }

        for ($y = 0; $y < $h; $y++) {
            $seed(0, $y);
            $seed($w - 1, $y);
        }

        while (! $queue->isEmpty()) {
            $i = $queue->dequeue();
            $x = $i % $w;
            $y = intdiv($i, $w);

            foreach ([[1, 0], [-1, 0], [0, 1], [0, -1]] as [$dx, $dy]) {
                $nx = $x + $dx;
                $ny = $y + $dy;

                if ($nx < 0 || $ny < 0 || $nx >= $w || $ny >= $h) {
                    continue;
                }

                $j = $ny * $w + $nx;

                if (! $bg[$j] && $lum[$j] <= $thr) {
                    $bg[$j] = 1;
                    $queue->enqueue($j);
                }
            }
        }

        $alpha = new \SplFixedArray($w * $h);

        for ($i = 0; $i < $w * $h; $i++) {
            $alpha[$i] = $bg[$i] ? 0 : 255;
        }

        // Dua kali box blur 3x3 supaya tepi potongannya tidak bergerigi.
        for ($pass = 0; $pass < 2; $pass++) {
            $next = new \SplFixedArray($w * $h);

            for ($y = 0; $y < $h; $y++) {
                for ($x = 0; $x < $w; $x++) {
                    $sum = 0;
                    $n = 0;

                    for ($dy = -1; $dy <= 1; $dy++) {
                        for ($dx = -1; $dx <= 1; $dx++) {
                            $nx = $x + $dx;
                            $ny = $y + $dy;

                            if ($nx < 0 || $ny < 0 || $nx >= $w || $ny >= $h) {
                                continue;
                            }

                            $sum += $alpha[$ny * $w + $nx];
                            $n++;
                        }
                    }

                    $next[$y * $w + $x] = (int) round($sum / $n);
                }
            }

            $alpha = $next;
        }

        // Pudar dari dada ke bawah. Sisa garis rim light di bahu dihapus halus
        // sehingga potongan badannya terbaca sebagai pilihan desain.
        $fs = (float) $this->option('fade-start');
        $fe = (float) $this->option('fade-end');

        for ($y = 0; $y < $h; $y++) {
            $t = ($y / $h - $fs) / max(0.0001, $fe - $fs);
            $k = 1 - max(0.0, min(1.0, $t));

            if ($k >= 1.0) {
                continue;
            }

            for ($x = 0; $x < $w; $x++) {
                $alpha[$y * $w + $x] = (int) round($alpha[$y * $w + $x] * $k);
            }
        }

        $out = imagecreatetruecolor($w, $h);
        imagealphablending($out, false);
        imagesavealpha($out, true);

        for ($y = 0; $y < $h; $y++) {
            for ($x = 0; $x < $w; $x++) {
                $i = $y * $w + $x;
                $a = 127 - (int) round(127 * $alpha[$i] / 255);
                imagesetpixel($out, $x, $y, ($a << 24) | (imagecolorat($im, $x, $y) & 0xFFFFFF));
            }
        }

        // Berkas asli dibiarkan utuh; hasilnya ditulis sebagai berkas baru.
        $rel = preg_replace('/\.[^.]+$/', '', $value).'-cutout.png';
        $dst = public_path($rel);

        imagepng($out, $dst, 9);
        @chmod($dst, 0664);

        $this->info('Cutout dibuat: '.$rel.' ('.number_format(filesize($dst) / 1024, 1).' KB)');

        if ($this->option('apply')) {
            PageContent::where('key', 'hero_image')->update(['value' => $rel]);
            $this->info('hero_image diarahkan ke cutout. Berkas asli: '.$value);
        } else {
            $this->line('Jalankan dengan --apply untuk memakainya sebagai foto hero.');
        }

        return self::SUCCESS;
    }
}
