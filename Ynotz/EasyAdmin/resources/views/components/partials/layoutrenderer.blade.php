@props(['item', 'form', '_old', 'errors'])
@switch($item['layout_type'])
    @case('tabs_panel')
            <x-easyadmin::partials.tabspanel
            :item="$item"
            :form="$form"
            :_old="$_old"
            :errors="$errors" />
        @break
    @case('row')
        <x-easyadmin::partials.row
            :item="$item"
            :form="$form"
            :_old="$_old"
            :errors="$errors" />
        @break
    @case('column')
        <x-easyadmin::partials.column
            :item="$item"
            :form="$form"
            :_old="$_old"
        :errors="$errors"/>
        @break
    @case('divider')
        <x-easyadmin::partials.section-divider
            :item="$item"/>
        @break
    @default
        <x-dynamic-component :component="$item['layout_type']"
            :item="$item"
            :form="$form"
            :_old="$_old"
            :errors="$errors" />
@endswitch

