<?php

namespace App\Support\Portfolio;

use Illuminate\Support\Str;

/**
 * Works out which network a pasted URL belongs to, so the right icon shows
 * without anyone having to pick it. Icons are Font Awesome 6 classes.
 */
class SocialPlatform
{
    /** key => [display name, icon class, host fragments] */
    private const PLATFORMS = [
        'github' => ['GitHub', 'fa-brands fa-github', ['github.com']],
        'gitlab' => ['GitLab', 'fa-brands fa-gitlab', ['gitlab.com']],
        'linkedin' => ['LinkedIn', 'fa-brands fa-linkedin-in', ['linkedin.com', 'lnkd.in']],
        'instagram' => ['Instagram', 'fa-brands fa-instagram', ['instagram.com', 'instagr.am']],
        'facebook' => ['Facebook', 'fa-brands fa-facebook-f', ['facebook.com', 'fb.com', 'fb.me']],
        'x' => ['X', 'fa-brands fa-x-twitter', ['twitter.com', 'x.com']],
        'threads' => ['Threads', 'fa-brands fa-threads', ['threads.net', 'threads.com']],
        'tiktok' => ['TikTok', 'fa-brands fa-tiktok', ['tiktok.com']],
        'youtube' => ['YouTube', 'fa-brands fa-youtube', ['youtube.com', 'youtu.be']],
        'whatsapp' => ['WhatsApp', 'fa-brands fa-whatsapp', ['wa.me', 'whatsapp.com']],
        'telegram' => ['Telegram', 'fa-brands fa-telegram', ['t.me', 'telegram.me', 'telegram.org']],
        'discord' => ['Discord', 'fa-brands fa-discord', ['discord.gg', 'discord.com']],
        'dribbble' => ['Dribbble', 'fa-brands fa-dribbble', ['dribbble.com']],
        'behance' => ['Behance', 'fa-brands fa-behance', ['behance.net']],
        'figma' => ['Figma', 'fa-brands fa-figma', ['figma.com']],
        'medium' => ['Medium', 'fa-brands fa-medium', ['medium.com']],
        'dev' => ['DEV', 'fa-brands fa-dev', ['dev.to']],
        'stackoverflow' => ['Stack Overflow', 'fa-brands fa-stack-overflow', ['stackoverflow.com']],
        'pinterest' => ['Pinterest', 'fa-brands fa-pinterest-p', ['pinterest.com', 'pin.it']],
        'spotify' => ['Spotify', 'fa-brands fa-spotify', ['spotify.com']],
        'twitch' => ['Twitch', 'fa-brands fa-twitch', ['twitch.tv']],
        'reddit' => ['Reddit', 'fa-brands fa-reddit-alien', ['reddit.com']],
        'upwork' => ['Upwork', 'fa-brands fa-upwork', ['upwork.com']],
        'fiverr' => ['Fiverr', 'fa-brands fa-fiverr', ['fiverr.com']],
        'codepen' => ['CodePen', 'fa-brands fa-codepen', ['codepen.io']],
        'npm' => ['npm', 'fa-brands fa-npm', ['npmjs.com']],
    ];

    public static function detect(?string $url): string
    {
        $url = trim((string) $url);

        if (Str::startsWith($url, 'mailto:')) {
            return 'email';
        }
        if (Str::startsWith($url, 'tel:')) {
            return 'phone';
        }

        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        $host = preg_replace('/^(www\.|m\.|mobile\.|web\.|api\.)/', '', $host);

        foreach (self::PLATFORMS as $key => [, , $hosts]) {
            foreach ($hosts as $candidate) {
                if ($host === $candidate || Str::endsWith($host, '.' . $candidate)) {
                    return $key;
                }
            }
        }

        return 'website';
    }

    public static function icon(?string $platform): string
    {
        return match ($platform) {
            'email' => 'fa-solid fa-envelope',
            'phone' => 'fa-solid fa-phone',
            'website', null => 'fa-solid fa-globe',
            default => self::PLATFORMS[$platform][1] ?? 'fa-solid fa-globe',
        };
    }

    public static function name(?string $platform): string
    {
        return match ($platform) {
            'email' => 'Email',
            'phone' => 'Telepon',
            'website', null => 'Website',
            default => self::PLATFORMS[$platform][0] ?? 'Website',
        };
    }

    /** Lookup table for the admin form's live icon preview. */
    public static function hostMap(): array
    {
        $map = [];
        foreach (self::PLATFORMS as $key => [$name, $icon, $hosts]) {
            foreach ($hosts as $host) {
                $map[$host] = ['name' => $name, 'icon' => $icon];
            }
        }

        return $map;
    }
}
