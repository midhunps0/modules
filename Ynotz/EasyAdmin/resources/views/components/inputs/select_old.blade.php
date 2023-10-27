@props([
    'name',
    'label',
    'options',
    'none_selected',
    'options_type' => 'key_value',
    'options_id_key' => 'id',
    'options_src_route' => null,
    'options_src_trigger' => null,
    'options_text_key' => null,
    'width' => 'full',
    'placeholder' => null,
    'wrapper_styles' => null,
    'input_styles' => null,
    'label_position' => 'top',
    'properties' => [],
    '_old' => [],
    'xerrors' => [],
])
@php
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
@endphp
<div x-data="{
        val: '',
        oldVal: null,
        options: [],
        loadOptions(val) {
            console.log('Have to implement loading code');
        },
        errors: ''
    }"
    x-init="
        @if (isset($_old[$name]))
            oldVal = '{{$_old[$name]}}';
        @endif
        ops = JSON.parse('{{json_encode($options)}}');
        @if ($options_type == 'key_value')
            Object.keys(ops).forEach((key) => {
                options.push({key: key, text: ops[key]});
            });
        @elseif ($options_type == 'value_only')
            ops.forEach((op) => {
                options.push({key: op, text: op });
            });
        @elseif ($options_type == 'collection')
            ops.forEach((op) => {
                options.push({key: op.{{$options_id_key}}, text: op.{{$options_text_key}} });
            });
        @endif
        @if ($xerrors->has($name))
            ers = {{json_encode($xerrors->get($name))}};
            errors = ers.reduce((r, e) => {
                return r + ' ' + e;
            }, '').trim();
        @endif
        @if (isset($properties['required']) && $properties['required'])
            required = true;
        @endif
    "
    @selectionchange.window="console.log($event.detail);if($event.detail.field == '{{$options_src_trigger}}') {
        loadOptions($event.detail.value);
    }"
    @class([
        'relative',
        'form-control',
        $wclass,
        'flex flex-row' => $label_position == 'side'
    ])>
    @if ($label_position != 'float')
        <label @class([
            'label',
            'justify-start',
            'w-36' => $label_position == 'side'
        ])>
            <span class="label-text">{{$label}}</span>@if (isset($properties['required']) && $properties['required'])
            &nbsp;<span class="text-warning">*</span>@endif
        </label>
    @endif
    <div  @class([
            'flex-grow' => $label_position == 'side',
            'w-full' => $label_position != 'side',
        ]) >
        <select x-model="val" name="{{$name}}" class="select select-bordered w-full peer"
            :class="errors.length == 0 || 'border-error border-opacity-50'"
            @change="$dispatch('selectionchange', {field: '{{$name}}',value: val});"
            @foreach ($properties as $prop => $val)
                @if (!is_bool($val))
                    {{$prop}}="{{$val}}"
                @elseif ($val)
                    {{$prop}}
                @endif
            @endforeach>
                <option x-show="val != ''" @selected(!isset($_old[$name])) value="" x-text=" val != '' ? '{{$none_selected}}' : ''"></option>
                <template x-for="op in options">
                    <option :value="op.key" x-text="op.text" :selected="oldVal == op.key"></option>
                </template>
        </select>
        @if ($label_position == 'float')
            {{-- <label x-show="val!=''" class="absolute text-warning peer-placeholder-shown:text-base-content duration-300 transform -translate-y-4 scale-90 top-2 left-2 z-10 origin-[0] bg-base-100 px-2 peer-focus:px-2 peer-focus:text-warning peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-90 peer-focus:-translate-y-4 transition-all">
                <span>{{$label}}</span>&nbsp;<span class="text-warning">*</span>
            </label> --}}
            <label
                class="absolute duration-300 bg-base-100 px-2 transition-all left-2"
                :class="val != '' ? 'text-warning transform -translate-y-4 scale-90 top-2 z-10 origin-[0]' : 'transform translate-y-2 top-2'">
                {{-- {{$label}} --}}
                <span>{{ $label }}</span>@if (isset($properties['required']) && $properties['required'])
                &nbsp;<span class="text-warning">*</span>@endif
            </label>
        @endif

        <x:easyadmin::partials.errortext />
    </div>
  </div>
