<?php

if (!function_exists('format_rupiah')) {
    /**
     * Format number to Indonesian Rupiah currency string.
     * Example: 125000 -> "Rp 125.000"
     */
    function format_rupiah($amount): string
    {
        return 'Rp ' . number_format((int) ($amount ?? 0), 0, ',', '.');
    }
}

if (!function_exists('tier_label')) {
    /**
     * Get human-readable label for price tier in Indonesian.
     */
    function tier_label(?string $tier): string
    {
        return match ($tier) {
            'retail' => 'Eceran',
            'wholesale' => 'Grosir',
            'super_wholesale' => 'Super Grosir',
            default => 'Standar',
        };
    }
}

