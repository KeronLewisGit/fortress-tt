@props(['name', 'alt' => '', 'class' => '', 'sizes' => '(min-width: 1024px) 50vw, 100vw', 'eager' => false])
<img src="{{ img($name) }}" srcset="{{ img($name, 900) }} 900w, {{ img($name) }} 1920w" sizes="{{ $sizes }}" alt="{{ $alt }}" {{ $eager ? 'fetchpriority=high' : 'loading=lazy' }} decoding="async" {{ $attributes->merge(['class' => 'h-full w-full object-cover '.$class]) }}>
