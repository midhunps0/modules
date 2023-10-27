@props(['item', 'form', '_old', 'errors'])
@php
    $wclasses = [
        '1/2' => 'w-1/2',
        '1/3' => 'w-1/3',
        '1/4' => 'w-1/4',
        '2/3' => 'w-2/3',
        '2/5' => 'w-2/5',
        '3/4' => 'w-3/4',
        '3/5' => 'w-3/5',
        'full' => 'w-full',
        'grow' => 'flex-grow'
    ];
    $width = isset($item['width']) && isset($wclasses[$item['width']]) ? $wclasses[$item['width']] : 'flex-grow';
@endphp
<div class="flex flex-row {{$width}}">
    <div x-data="{
        currentTab: 0,
        }" class="w-full">
        <div class="tabs w-full overflow-y-auto">
            @foreach ($item['tabs'] as $tab)
            <a class="tab w-1/3 tab-lifted" :class="currentTab != {{$loop->index}} || 'tab-active !bg-base-200'">{{$tab['title']}}</a>
            @endforeach
            {{-- <a class="tab tab-lifted tab-active">Tab 2</a> --}}
        </div>
        <div class="p-4 border-r border-b border-l border-base-300 rounded-b-md">
        @foreach ($item['tabs'] as $tab)
            @if ($tab['item_type'] == 'layout' && $tab['layout_type'] == 'tab')
            <div x-show="currentTab == {{$loop->index}}" class="flex flex-row w-full">
            @foreach ($tab['items'] as $tab_item)
                @if ($tab_item['item_type'] == 'layout')
                    <x-easyadmin::partials.layoutrenderer
                        :item="$tab_item"
                        :form="$form"
                        :_old="$_old"
                        :errors="$errors"  />
                @elseif ($tab_item['item_type'] == 'input')
                    <x-easyadmin::partials.inputrenderer
                        :item="$form_items[$tab_item['key']]"
                        :form="$form"
                        :_old="$_old"
                        :errors="$errors" />
                @endif
            @endforeach
            </div>
            @endif
        @endforeach
        </div>
    </div>
</div>
