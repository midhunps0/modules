@props([
    'element',
    '_old' => [],
    '_current_values' => [],
    'xerrors' => [],
    'label_position' => 'top',
])
@php
    $type = $element['input_type'];
    $name = $element['key'];
    $label = $element['label'];
    $authorised = $element['authorised'] ?? true;
    $width = $element['width'] ?? 'full';
    $placeholder = $element["placeholder"] ?? null;
    $wrapper_styles = $element["wrapper_styles"] ?? null;
    $input_styles = $element["input_styles"] ?? null;
    $properties = $element['properties'] ?? [];
    $fire_input_event = $element['fire_input_event'] ?? false;
    $update_on_events = $element['update_on_events'] ?? null;
    $reset_on_events = $element['reset_on_events'] ?? null;
    $toggle_on_events = $element['toggle_on_events'] ?? null;
    $show = $element['show'] ?? true;

    $wclass = 'w-full';
    switch ($width) {
        case 'full':
            $wclass = 'w-full';
            break;
        case '1/2':
            $wclass = 'w-1/2';
            break;
        case '1/3':
            $wclass = 'w-1/3';
            break;
        case '2/3':
            $wclass = 'w-2/3';
            break;
        case '1/4':
            $wclass = 'w-1/4';
            break;
        case '3/4':
            $wclass = 'w-3/4';
            break;
    }
@endphp
@if ($authorised)

<div x-data="{
        textval: '',
        errors: '',
        required: false,
        listeners: {},
        resetSources: [],
        toggleListeners: {},
        showelement: true,
        updateOnEvent(source, value) {
            if (Object.keys(this.listeners).includes(source)) {
                if (this.listeners[source].serviceclass == null) {
                    this.textval = '';
                } else {
                    let url = '{{route('easyadmin.fetch', ['service' => '__service__', 'method' => '__method__'])}}';
                    url = url.replace('__service__', this.listeners[source].serviceclass);
                    url = url.replace('__method__', this.listeners[source].method);
                    axios.get(
                        url,
                        {
                            params: {'value': value}
                        }
                    ).then((r) => {
                        this.textval = r.data.results;
                    }).catch((e) => {
                        console.log(e);
                    });
                }
            }
        },
        toggleOnEvent(source, value) {
            if (Object.keys(this.toggleListeners).includes(source)) {
                this.toggleListeners[source].forEach((item) => {
                    switch(item.condition) {
                        case '==':
                            if (item.value == value) {
                                this.showelement = item.show;
                            }
                            break;
                        case '!=':
                            if (item.value != value) {
                                this.showelement = item.show;
                            }
                            break;
                        case '>':
                            if (item.value > value) {
                                this.showelement = item.show;
                            }
                            break;
                        case '<':
                            if (item.value < value) {
                                this.showelement = item.show;
                            }
                            break;
                        case '>=':
                            if (item.value >= value) {
                                this.showelement = item.show;
                            }
                            break;
                        case '<=':
                            if (item.value <= value) {
                                this.showelement = item.show;
                            }
                            break;
                    }
                });
            }
        },
        resetOnEvent(detail) {
            if(this.resetSources.includes(detail.source)) {
                this.reset();
            }
        },
        reset() {
            this.textval = '';
            this.errors = '';
        }
    }"
    x-init="
        @if (!$show)
            showelement =  false;
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
        @if (isset($update_on_events))
            @foreach ($update_on_events as $source => $api)
                listeners.{{$source}} = {
                    serviceclass: @if (isset($api[0])) '{{$api[0]}}' @else null @endif,
                    method: @if (isset($api[1])) '{{$api[1]}}' @else null @endif,
                };
            @endforeach
            console.log('{{$name}} listeners: ');
            console.log(listeners);
        @endif
        @if (isset($reset_on_events))
            @foreach ($reset_on_events as $source)
                resetSources.push('{{$source}}');
            @endforeach
        @endif
        @if (isset($toggle_on_events))
        @foreach ($toggle_on_events as $source => $conditions)
            toggleListeners.{{$source}} = [];
            @foreach ($conditions as $condition)
                toggleListeners.{{$source}}.push({
                    condition: '{{$condition[0]}}',
                    value: '{{$condition[1]}}',
                    show: {{$condition[2] ? 'true' : 'false'}},
                });
            @endforeach
        @endforeach
        @endif
        @if (isset($_old[$name]))
            textval = '{{$_old[$name]}}';
        @endif
        $watch('showelement', function (val) {required = val; console.log('text required'); console.log(val);});
    "
    @class([
        'relative',
        'form-control',
        $wclass,
        'flex flex-row' => $label_position == 'side'
    ])

    @if (isset($update_on_events) || isset($toggle_on_events))
    @eaforminputevent.window="@if (isset($update_on_events))updateOnEvent($event.detail.source, $event.detail.value);@endif @if(isset($toggle_on_events))toggleOnEvent($event.detail.source, $event.detail.value);@endif"
    @endif

    @formerrors.window="if (Object.keys($event.detail.errors).includes('{{$name}}')) {
        errors = $event.detail.errors['{{$name}}'].reduce((result, e) => {
            if (result.length > 0) {
                return result + ' ' + e;
            } else {
                return result + e;
            }
        }, '');
    } else {
        errors = '';
    }"
    @if (isset($reset_on_events) && count($reset_on_events) > 0)
    @eaforminputevent.window="resetOnEvent($event.detail);"
    @endif
    x-show="showelement" x-transition.duration.300ms
    >
    @if ($type != 'hidden' && $label_position != 'float')
    <label for="{{$name}}" @class([
            'label',
            'justify-start',
            'w-36' => $label_position == 'side'
        ])>
        <span class="label-text">{{$label}}</span>@if (isset($properties['required']) && $properties['required'])
        &nbsp;<span class="text-warning">*</span>@endif
    </label>
    @endif
    <div @class([
            'flex-grow' => $label_position == 'side',
            'w-full' => $label_position != 'side',
        ]) >
        <input
            id="{{$name}}" x-model="textval" type="{{$type}}" name="{{$name}}"
            @if ($label_position == 'float')
            placeholder="' '"
            @else
            placeholder="{{$placeholder ?? ''}}"
            @endif
            class="peer input w-full input-bordered"
            :class="errors.length == 0 || 'border-error  border-opacity-50'"
            value="{{ $_current_values[$name] ?? ($_old[$name] ?? '') }}"
            @foreach ($properties as $prop => $val)
                @if (!in_array($prop, ['required']))
                    @if (!is_bool($val))
                        {{$prop}}="{{$val}}"
                    @elseif ($val)
                        {{$prop}}
                    @endif
                @endif
            @endforeach
            @if ($fire_input_event)
                @change="$dispatch('eaforminputevent', {source: '{{$name}}', value: textval});"
            @endif
            :required="required"
            />

        @if ($type != 'hidden' && $label_position == 'float')
        <label for="{{$name}}" class="absolute text-warning peer-placeholder-shown:text-base-content duration-300 transform -translate-y-4 scale-90 top-2 left-2 z-10 origin-[0] bg-base-100 px-2 peer-focus:px-2 peer-focus:text-warning peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-90 peer-focus:-translate-y-4 transition-all">
            {{-- {{$label}} --}}
            <span>{{$label}}</span>@if (isset($properties['required']) && $properties['required'])
            &nbsp;<span class="text-warning">*</span>
            @endif
        </label>
        @endif

        <x:easyadmin::partials.errortext />
    </div>
</div>

@endif
