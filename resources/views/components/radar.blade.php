@props(['class' => 'h-56 w-56'])
<div {{ $attributes->merge(['class' => 'radar '.$class]) }} role="img" aria-label="Animated radar sweep representing live 24-hour monitoring">
    <span class="radar-ring"></span><span class="radar-ring"></span><span class="radar-ring"></span><span class="radar-ring"></span>
    <span class="radar-axis"></span><span class="radar-axis h"></span>
    <span class="radar-pulse"></span>
    <span class="radar-blip" style="left:68%;top:30%;--d:.4s"></span>
    <span class="radar-blip amber" style="left:30%;top:62%;--d:1.6s"></span>
    <span class="radar-blip" style="left:58%;top:74%;--d:2.7s"></span>
    <span class="radar-blip" style="left:22%;top:28%;--d:3.3s"></span>
    <span class="absolute left-1/2 top-1/2 h-2.5 w-2.5 -translate-x-1/2 -translate-y-1/2 rounded-full bg-blue-400 shadow-[0_0_14px_3px_rgb(59_139_255/0.7)]"></span>
</div>
