@props(['status', 'label' => null])
<span {{ $attributes->merge(['class' => 'stage stage-'.$status]) }}>{{ $label ?? ucfirst($status) }}</span>
