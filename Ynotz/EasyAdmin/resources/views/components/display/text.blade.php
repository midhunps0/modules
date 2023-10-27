@props(['row_data', 'col'])
<td>
    <span>
        @if (isset($col['relation']))
            @php
                $r = $col['relation'];
            @endphp
            @if ($row_data->$r instanceof \Illuminate\Support\Collection)
                @foreach ($row_data->$r as $relation)
                    @foreach ($col['fields'] as $field)
                    {{$relation->$field}}
                    @endforeach
                    @if (!$loop->last && trim($relation->$field) != ''),@endif
                @endforeach
            @elseif ($row_data->$r instanceof \Illuminate\Database\Eloquent\Model)
                @foreach ($col['fields'] as $field)
                    {{$row_data->$r->$field}}
                @endforeach
            @endif
        @else
            @foreach ($col['fields'] as $field)
                @if (isset($col['link']))
                    @php
                        $k = $col['link']['key'];
                        $key = $row_data->$k ?? 'id';
                    @endphp
                <a x-data @click.prevent.stop="$dispatch('linkaction', { link: '{{route($col['link']['route'], $key)}}', route: '{{$col['link']['route']}}'})" class="cursor-pointer hover:underline">
                @endif
                {{$row_data->$field}}@if (!$loop->last && trim($row_data->$field) != ''),@endif
                @if (isset($col['link']))
                    </a>
                @endif
            @endforeach
        @endif
    </span>
</td>
