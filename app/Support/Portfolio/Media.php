<?php

namespace App\Support\Portfolio;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Stores portfolio uploads under public/uploads/portfolio.
 *
 * Images are re-encoded with GD: auto-rotated from EXIF, capped at a sane
 * display width, written as WebP at a visually lossless quality and stripped
 * of metadata. When re-encoding would not make the file smaller (an already
 * optimised image that needs no resize) the original bytes are kept instead,
 * so compression never costs quality or size.
 */
class Media
{
    public const ROOT = 'uploads/portfolio';

    /** Upload ceiling for every file, in kilobytes (10 MB). */
    public const MAX_KB = 10240;

    public const IMAGE_MIMES = 'jpg,jpeg,png,webp,gif';

    public const FILE_MIMES = 'pdf,zip,rar,7z,doc,docx,xls,xlsx,ppt,pptx,txt,csv,fig,psd,ai,jpg,jpeg,png,webp,gif,mp4';

    private const QUALITY = 86;

    /**
     * @return array{path:string, thumb:string|null, width:int, height:int}
     */
    public static function storeImage(UploadedFile $file, string $folder, int $maxWidth = 2400, ?int $thumbWidth = 800): array
    {
        $dir = self::directory($folder);
        $name = Str::random(32);
        $mime = (string) $file->getMimeType();

        // Animated GIFs would lose their frames through GD.
        if ($mime === 'image/gif') {
            $path = self::moveOriginal($file, $dir, $name);
            [$w, $h] = @getimagesize(public_path($path)) ?: [0, 0];

            return ['path' => $path, 'thumb' => null, 'width' => (int) $w, 'height' => (int) $h];
        }

        @ini_set('memory_limit', '512M');

        $image = self::load($file->getRealPath(), $mime);
        $image = self::orient($image, $file->getRealPath(), $mime);

        $width = imagesx($image);
        $height = imagesy($image);

        $main = self::resized($image, $maxWidth);
        $mainPath = $dir . '/' . $name . '.webp';
        imagewebp($main, public_path($mainPath), self::QUALITY);

        // Keep the original when it is already smaller and needed no resize.
        if ($main === $image && filesize(public_path($mainPath)) >= $file->getSize()) {
            @unlink(public_path($mainPath));
            $mainPath = self::moveOriginal($file, $dir, $name, false);
        }

        $thumbPath = null;
        if ($thumbWidth !== null && $width > $thumbWidth) {
            $thumb = self::resized($image, $thumbWidth);
            $thumbPath = $dir . '/' . $name . '-thumb.webp';
            imagewebp($thumb, public_path($thumbPath), 82);
            imagedestroy($thumb);
        }

        $finalWidth = imagesx($main);
        $finalHeight = imagesy($main);

        if ($main !== $image) {
            imagedestroy($main);
        }
        imagedestroy($image);

        return ['path' => $mainPath, 'thumb' => $thumbPath, 'width' => $finalWidth, 'height' => $finalHeight];
    }

    /**
     * @return array{path:string, original_name:string, mime:string, size:int}
     */
    public static function storeFile(UploadedFile $file, string $folder): array
    {
        $dir = self::directory($folder);
        $original = Str::limit(preg_replace('/[^\w.\- ]+/u', '', $file->getClientOriginalName()) ?: 'file', 190, '');
        $size = (int) $file->getSize();
        $mime = (string) $file->getMimeType();

        return [
            'path' => self::moveOriginal($file, $dir, Str::random(32)),
            'original_name' => $original,
            'mime' => $mime,
            'size' => $size,
        ];
    }

    public static function url(?string $path, ?string $fallback = null): string
    {
        if (blank($path)) {
            return $fallback ? asset($fallback) : '';
        }

        if (Str::startsWith($path, ['http://', 'https://', '//'])) {
            return $path;
        }

        return asset(ltrim($path, '/'));
    }

    /** Remove files — only ever inside the upload root. */
    public static function delete(?string ...$paths): void
    {
        foreach ($paths as $path) {
            if (blank($path) || ! Str::startsWith($path, self::ROOT . '/') || str_contains($path, '..')) {
                continue;
            }

            File::delete(public_path($path));
        }
    }

    // ------------------------------------------------------------------

    private static function directory(string $folder): string
    {
        $dir = self::ROOT . '/' . Str::slug($folder) . '/' . now()->format('Y/m');
        File::ensureDirectoryExists(public_path($dir));

        return $dir;
    }

    private static function moveOriginal(UploadedFile $file, string $dir, string $name, bool $move = true): string
    {
        $ext = strtolower($file->guessExtension() ?: $file->getClientOriginalExtension() ?: 'bin');
        $ext = preg_replace('/[^a-z0-9]/', '', $ext) ?: 'bin';
        $path = $dir . '/' . $name . '.' . $ext;

        $move
            ? $file->move(public_path($dir), $name . '.' . $ext)
            : copy($file->getRealPath(), public_path($path));

        return $path;
    }

    /** @return \GdImage */
    private static function load(string $source, string $mime)
    {
        $image = match ($mime) {
            'image/jpeg', 'image/jpg', 'image/pjpeg' => @imagecreatefromjpeg($source),
            'image/png' => @imagecreatefrompng($source),
            'image/webp' => @imagecreatefromwebp($source),
            default => false,
        };

        if (! $image) {
            throw new RuntimeException('Format gambar tidak didukung atau file rusak.');
        }

        if (! imageistruecolor($image)) {
            imagepalettetotruecolor($image);
        }
        imagealphablending($image, true);
        imagesavealpha($image, true);

        return $image;
    }

    /** Honour the camera's EXIF orientation before metadata is dropped. */
    private static function orient($image, string $source, string $mime)
    {
        if (! in_array($mime, ['image/jpeg', 'image/jpg', 'image/pjpeg'], true) || ! function_exists('exif_read_data')) {
            return $image;
        }

        $orientation = (int) (@exif_read_data($source)['Orientation'] ?? 1);
        $angle = [3 => 180, 6 => -90, 8 => 90][$orientation] ?? 0;

        if ($angle === 0) {
            return $image;
        }

        $rotated = imagerotate($image, $angle, 0);
        imagedestroy($image);

        return $rotated;
    }

    /** A downscaled copy, or the same resource when it already fits. */
    private static function resized($image, int $maxWidth)
    {
        $width = imagesx($image);
        if ($width <= $maxWidth) {
            return $image;
        }

        $height = (int) round(imagesy($image) * $maxWidth / $width);
        $canvas = imagecreatetruecolor($maxWidth, $height);
        imagealphablending($canvas, false);
        imagesavealpha($canvas, true);
        imagefill($canvas, 0, 0, imagecolorallocatealpha($canvas, 0, 0, 0, 127));
        imagecopyresampled($canvas, $image, 0, 0, 0, 0, $maxWidth, $height, $width, imagesy($image));

        return $canvas;
    }
}
