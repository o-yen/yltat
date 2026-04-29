<?php

namespace App\Services;

class DataMaskingService
{
    /**
     * Mask IC number: 991225-XX-XXXX → XXXXXX-XX-XXXX
     */
    public static function maskIcNumber(?string $value): string
    {
        if (!$value) return '-';
        return 'XXXXXX-XX-XXXX';
    }

    /**
     * Mask phone: 012-3456789 → 012-XXX6789
     */
    public static function maskPhone(?string $value): string
    {
        if (!$value || strlen($value) < 6) return '-';
        $len = strlen($value);
        $visible = 3;
        $tail = 4;
        $masked = substr($value, 0, $visible) . str_repeat('X', $len - $visible - $tail) . substr($value, -$tail);
        return $masked;
    }

    /**
     * Mask address: return state only
     */
    public static function maskAddress(?string $value, ?string $state = null): string
    {
        if ($state) return $state;
        if (!$value) return '-';
        // Try to extract state from address
        $states = ['Johor', 'Kedah', 'Kelantan', 'Melaka', 'Negeri Sembilan', 'Pahang',
                   'Perak', 'Perlis', 'Pulau Pinang', 'Sabah', 'Sarawak', 'Selangor',
                   'Terengganu', 'WP Kuala Lumpur', 'WP Putrajaya', 'WP Labuan'];
        foreach ($states as $s) {
            if (stripos($value, $s) !== false) return $s;
        }
        return 'Malaysia';
    }

    /**
     * Mask allowance: show range only
     */
    public static function maskAllowance($value): string
    {
        return 'RM 1,500 – 2,000';
    }

    /**
     * Mask name: Ahmad bin Ali → A***** bin A**
     */
    public static function maskName(?string $value): string
    {
        if (!$value) return '-';
        $parts = explode(' ', $value);
        $masked = array_map(function ($p) {
            if (strlen($p) <= 3) return $p;
            return substr($p, 0, 1) . str_repeat('*', strlen($p) - 1);
        }, $parts);
        return implode(' ', $masked);
    }

    /**
     * Mask email: user@mail.com → u***@m***.com
     */
    public static function maskEmail(?string $value): string
    {
        if (!$value || !str_contains($value, '@')) return '-';
        [$local, $domain] = explode('@', $value, 2);
        $maskedLocal = substr($local, 0, 1) . '***';
        $dotPos = strrpos($domain, '.');
        if ($dotPos !== false) {
            $maskedDomain = substr($domain, 0, 1) . '***' . substr($domain, $dotPos);
        } else {
            $maskedDomain = substr($domain, 0, 1) . '***';
        }
        return "$maskedLocal@$maskedDomain";
    }

    /**
     * Check if current user requires data masking
     */
    public static function shouldMask(): bool
    {
        $role = auth()->user()?->role?->role_name;
        return $role === 'mindef_viewer';
    }

    /**
     * Apply masking if needed, otherwise return original
     */
    public static function mask(string $type, $value, $extra = null): string
    {
        if (!self::shouldMask()) {
            return (string) ($value ?? '-');
        }

        return match ($type) {
            'ic' => self::maskIcNumber($value),
            'phone' => self::maskPhone($value),
            'address' => self::maskAddress($value, $extra),
            'allowance' => self::maskAllowance($value),
            default => (string) ($value ?? '-'),
        };
    }
}
