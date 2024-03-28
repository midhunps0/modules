@props(['row_data', 'col'])
{{-- {{dd($row_data->current_translation, $col)}} --}}
@php
    $unique_key = $col['unique_key'] ?? 'id';
    if (isset($col['view_route_unique_key'])) {
        $route_param = null;
        $thedata = $row_data;
        if (strpos($col['view_route_unique_key'], '.')) {
            $arr = explode('.', $col['view_route_unique_key']);
            for($i = 0; $i < count($arr); $i++) {
                $route_param = $thedata[$arr[$i]];
                $thedata = $route_param;
            }

        }
        $viewLink = '#';
        // dd($route_param);
        if (isset($col['view_route_slug'])) {
            $viewLink = route($col['view_route'], [$col['view_route_slug'] => $route_param]);
        }
    }
@endphp

<td class="sticky !left-36 z-20">
    <div class="flex flex-row justify-start space-x-4 items-center">
        @if (isset($col['view_route']))
        <a href="{{$viewLink}}" target="_blank"
            class="btn btn-ghost btn-xs text-warning capitalize">
            <x-easyadmin::display.icon icon="easyadmin::icons.view_on" height="h-4" width="w-4"/>
        </a>
        @endif
        @if (isset($col['edit_route']))
        <a href=""
            @click.prevent.stop="$dispatch('linkaction', {link: '{{route($col['edit_route'], $row_data->$unique_key)}}', route: '{{$col['edit_route']}}', fresh: true});"
            class="btn btn-ghost btn-xs text-warning capitalize">
            <x-easyadmin::display.icon icon="easyadmin::icons.edit" height="h-4" width="w-4"/>
        </a>
        @endif
        @if (isset($col['delete_route']))
        <button @click.prevent.stop="$dispatch('deleteitem', {url: '{{route($col['delete_route'], $row_data->$unique_key)}}', itemid: {{$row_data->$unique_key}}});" class="btn btn-ghost btn-xs text-error capitalize"><x-easyadmin::display.icon icon="easyadmin::icons.delete" height="h-4" width="w-4"/></button>
        @endif
    </div>
</td>
