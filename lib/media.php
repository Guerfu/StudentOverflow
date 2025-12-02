<?php
// lib/media.php

function base_url(): string
{
    // SCRIPT_NAME is like /STUDENTOVERFLOW/posts/create.php or /STUDENTOVERFLOW/public/index.php
    // dirname(dirname(...)) trims the last two path segments to land at /STUDENTOVERFLOW
    $base = rtrim(str_replace('\\', '/', dirname(dirname($_SERVER['SCRIPT_NAME'] ?? '/'))), '/');
    return $base === '' ? '/' : $base;
}

/**
 * Shorten long text safely for previews.
 */
function excerpt(string $text, int $length = 220): string
{
    $text = trim(strip_tags($text));
    if (mb_strlen($text) <= $length) return $text;
    return mb_substr($text, 0, $length - 1) . '…';
}


function resolve_image_url(?string $file): ?string
{
    if (!$file) return null;

    // Already a full URL?
    if (preg_match('~^https?://~i', $file)) {
        return $file;
    }

    // Normalize to filename only (strip any directory parts)
    $file = preg_replace('~.*[\\\\/]~', '', $file);

    // Build candidate filesystem paths and corresponding web URLs
    $rootFs   = dirname(__DIR__);          // .../STUDENTOVERFLOW
    $publicFs = $rootFs . '/public';       // .../STUDENTOVERFLOW/public
    $base     = base_url();                // /STUDENTOVERFLOW (correct casing)

    $candidates = [
        [$rootFs   . '/uploads/' . $file, $base . '/uploads/' . rawurlencode($file)],
        [$publicFs . '/uploads/' . $file, $base . '/public/uploads/' . rawurlencode($file)],
    ];

    foreach ($candidates as [$fs, $url]) {
        if (is_file($fs)) {
            return $url;
        }
    }

    return null; // Not found on disk
}

function uploads_dir_fs(): string
{
    return dirname(__DIR__) . '/uploads';
}
