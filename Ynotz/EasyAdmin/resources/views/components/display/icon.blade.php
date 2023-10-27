@props(['icon', 'height' => 'h-4', 'width' => 'w-4'])
<span {{ $attributes->class(['inline-block', $height,  $width]) }}>
    {!! __($icon) !!}
</span>