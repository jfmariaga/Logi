@props(['click' => null, 'href' => null, 'color' => 'secondary', 'classextra' => ''])

<a href="{{ $href ?? 'javascript:' }}" @if ($click) x-on:click="{{ $click }}" @endif
    {{ $attributes->merge([
        'class' => "border_none btn btn-sm grey btn-outline-$color $classextra",
    ]) }}
    style="padding: 3px;">
    {{ $slot }}
</a>
