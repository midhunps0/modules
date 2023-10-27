@props(['item', 'form', '_old', 'errors'])
@switch($item['item_type'])
    @case('input')
        @php
            $html_inputs = [
                'button',
                'checkbox',
                'color',
                'date',
                'datetime-local',
                'email',
                // "file",
                'hidden',
                // "image",
                'month',
                'number',
                'password',
                // "radio",
                // "range",
                // "reset",
                'search',
                // "submit",
                'tel',
                'text',
                'time',
                'url',
                'week',
            ];
        @endphp
        @if (in_array($item['input_type'], $html_inputs))
            <x-dynamic-component :component="'easyadmin::inputs.text'" :element="$item" :label_position="$form['label_position'] ?? 'top'" :_old="$_old ?? []" :xerrors="$errors ?? []" />
        @else
            <x-dynamic-component :component="$item['input_type']" :element="$item" :label_position="$form['label_position'] ?? 'top'" :_old="$_old ?? []" :xerrors="$errors ?? []" />
        @endif

        @default
        @break

    @endswitch
