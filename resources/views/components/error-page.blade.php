@props(['code', 'title', 'message'])
<x-layouts.public :title="$title">
    <section class="py-28"><div class="container-x max-w-xl text-center"><p class="font-display text-8xl font-extrabold text-blue-200">{{ $code }}</p><h1 class="h2 mt-2 text-navy-900">{{ $title }}</h1><p class="mt-4 text-steel-600">{{ $message }}</p><div class="mt-8 flex justify-center gap-3"><a href="{{ route('home') }}" class="btn-navy">Home</a><a href="{{ route('contact') }}" class="btn-outline">Contact us</a></div></div></section>
</x-layouts.public>
