<?php

use App\Models\Service;
use App\Models\Setting;
use Illuminate\Support\Str;

if (! function_exists('setting')) {
    function setting(string $key, mixed $default = null): mixed
    {
        return Setting::get($key, $default);
    }
}

if (! function_exists('fortress')) {
    function fortress(string $key, mixed $default = null): mixed
    {
        return config('fortress.'.$key, $default);
    }
}

if (! function_exists('tel_href')) {
    /** tel: link for a Trinidad & Tobago number written any common way. */
    function tel_href(?string $number): string
    {
        $digits = preg_replace('/\D+/', '', (string) $number);
        if (strlen($digits) === 7) {
            $digits = '868'.$digits;
        }
        if (strlen($digits) === 10) {
            $digits = '1'.$digits;
        }

        return 'tel:+'.$digits;
    }
}

if (! function_exists('whatsapp_href')) {
    function whatsapp_href(?string $number, ?string $text = null): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $number);
        if (! $digits) {
            return null;
        }
        if (strlen($digits) === 7) {
            $digits = '868'.$digits;
        }
        if (strlen($digits) === 10) {
            $digits = '1'.$digits;
        }
        $url = 'https://wa.me/'.$digits;

        return $text ? $url.'?text='.rawurlencode($text) : $url;
    }
}

if (! function_exists('notification_addresses')) {
    /** Recipients for a settings key holding one or more addresses. */
    function notification_addresses(string $key = 'notifications.recruitment_email'): array
    {
        return array_values(array_filter(
            preg_split('/[\s,;]+/', (string) setting($key), -1, PREG_SPLIT_NO_EMPTY),
            fn ($a) => filter_var($a, FILTER_VALIDATE_EMAIL)
        ));
    }
}

if (! function_exists('unique_slug')) {
    function unique_slug(string $model, string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'item';
        $slug = $base;
        $i = 2;
        while ($model::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}

if (! function_exists('lines_to_array')) {
    /** Textarea with one item per line → clean array. */
    function lines_to_array(?string $text): array
    {
        return array_values(array_filter(array_map(fn ($l) => trim(preg_replace('/^(?:[-*]|•)\s*/u', '', $l)), preg_split('/\r\n|\r|\n/', (string) $text)), fn ($l) => $l !== ''));
    }
}

if (! function_exists('img')) {
    /** URL for a bundled WebP photo in public/assets/img (name without extension). */
    function img(string $name, ?int $width = null): string
    {
        return asset('assets/img/'.$name.($width ? '-'.$width : '').'.webp');
    }
}

if (! function_exists('service_image')) {
    function service_image(Service $service): ?string
    {
        return config('fortress.service_images.'.$service->slug);
    }
}
