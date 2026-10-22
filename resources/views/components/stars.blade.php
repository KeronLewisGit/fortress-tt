@props(['rating' => 0, 'class' => 'h-4 w-4'])
<span class="inline-flex items-center gap-0.5" title="{{ $rating ? $rating.'/5' : 'Not rated' }}">
@for($i = 1; $i <= 5; $i++)
    <svg class="{{ $class }} {{ $i <= (int) $rating ? 'text-amber-400' : 'text-steel-300' }}" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3 6.5 7 .9-5.1 4.9 1.3 7L12 18l-6.2 3.3 1.3-7L2 9.4l7-.9z"/></svg>
@endfor
</span>
