@props(['columns' => [], 'searches', 'sorts', 'filters'])

@foreach ($columns as $col)
@php
    $align = 'text-left';
    if (isset($col['align'])) {
        switch ($col['align']) {
            case 'right':
                $align = 'text-right';
                break;
            case 'center':
                $align = 'text-center';
                break;
            default:
                break;
        }
    }
@endphp
<th @if (isset($col['style']))
    style="{{$col['style']}}"
@endif>
    <div class="flex flex-row items-center">
        @if (isset($col['sort']))
            <x-easyadmin::utils.spotsort name="{{$col['sort']['key']}}" val="{{ $sorts[$col['sort']['key']] ?? 'none' }}" />
        @endif
        <div class="relative flex-grow ml-2  text-sm normal-case text-primary @if (isset($col['align']))
            {{$align}}
        @endif">
            {{$col['title']}}
            @if (isset($col['search']))
                <x-easyadmin::utils.spotsearch textval="{{ $searches[$col['search']['key']] ?? '' }}" textname="{{$col['search']['key']}}" label="{{ $col['search']['label'] ?? '' }}" condition="{{$col['search']['condition']}}" />
            @endif

            @if (isset($col['filter']))
                <x-easyadmin::utils.spotfilter :options="$col['filter']['options']"
                    :fieldname="$col['filter']['key']" selectedoption="{{$filters[$col['filter']['key']] ?? '' }}"/>
            @endif
        </div>
    </div>
</th>
@endforeach
