<?php

if (!function_exists('media_url')) {
    function media_url(?string $path, string $dir = 'uploads/posters/'): string
    {
        $path = trim((string) $path);
        if ($path === '') return '';
        if (preg_match('#^https?://#i', $path)) return $path;
        if (str_starts_with($path, 'assets/') || str_starts_with($path, 'uploads/')) return base_url($path);
        return base_url($dir . $path);
    }
}