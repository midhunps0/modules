@props([
    'element',
    '_old' => [],
    'xerrors' => [],
    'label_position' => 'top',
])
@php
    $name = $element['key'];
    $authorised = $element['authorised'];
    $label = $element['label'];
    $width = $element['width'] ?? 'full';
    $placeholder = $element["placeholder"] ?? null;
    $wrapper_styles = $element["wrapper_styles"] ?? null;
    $input_styles = $element["input_styles"] ?? null;
    $properties = $element['properties'] ?? [];
    $fire_input_event = $element['fire_input_event'] ?? false;
    $update_on_events = $element['update_on_events'] ?? null;
    $toggle_on_events = $element['toggle_on_events'] ?? null;
    $show = $element['show'] ?? true;

    $wclass = 'w-full';
    switch($width) {
        case '1/2':
            $wclass = 'w-1/2';
            break;
        case '1/3':
            $wclass = 'w-1/3';
            break;
        case '1/4':
            $wclass = 'w-1/4';
            break;
    }
    if ($label_position == 'side') {
        $wclass .= ' my-6 flex flex-row';
    } else {
        $wclass .= ' my-4';
    }
    $elid = Illuminate\Support\Str::ulid();
@endphp
@if ($authorised)
<div x-data="filepond"
    x-init="
        inputElement = document.getElementById('{{$elid}}');
        pond = createFilepondInput(inputElement);
        @if (isset($properties['required']) && $properties['required'])
            required = true;
        @endif
        @if (isset($properties['multiple']) && $properties['multiple'])
            multiple = true;
        @endif
        @if (isset($_old[$name]))
            oldFiles = [];
            @foreach ($_old[$name] as $f)
                oldFiles.push({
                    source: '{{$f}}',
                    options: {
                        type: 'local'
                    }
                });
            @endforeach
        @endif
        pond.setOptions({
            name: '{{$name}}',
            stylePanelLayout: 'compact',
            labelIdle: 'Drag & drop or browse',
            credits: false,
            required: required,
            allowMultiple: multiple,
            files: oldFiles,
            server: {
                process: '{{route('easyadmin.filepond_upload', ['name' => $name])}}',
                revert: '{{route('easyadmin.filepond_delete')}}',
            },
        });
    "
    @class([
            'relative',
            'form-control',
            $wclass,
            'my-6 flex flex-row' => $label_position == 'side'
        ])
    >
    <label for="{{$name}}"
        @class([
            'label',
            'justify-start',
            'w-36' => $label_position == 'side'
        ])>
        <span class="label-text">{{$label}}</span>@if (isset($properties['required']) && $properties['required'])
        &nbsp;<span class="text-warning">*</span>@endif
    </label>
    <div class="flex-grow h-fit relative">
        <input type="file" id="{{$elid}}">
    </div>
</div>
@endif

{{-- @once
    @push('header_js')
    <script src="{{asset('js/filepond.min.js')}}"></script>
    @endpush
    @push('css')
    <link href="{{asset('css/filepond.min.css')}}" rel="stylesheet" />
    @endpush
    @push('js')
    <script>
        const inputElement = document.getElementById('{{$elid}}');
        const pond = FilePond.create(inputElement);
        pond.setOptions({
            name: '{{$name}}',
            className: '{{$wclass}} border border-base-content border-opacity-50',
            credits: false
        });
    </script>
    @endpush
@endonce --}}
