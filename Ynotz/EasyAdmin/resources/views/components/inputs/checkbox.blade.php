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
    $authorised = $element['authorised'];
    $displayText = $element['display_text'];
    $label = $element['label'];
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
        elvalue: false,
        displayText: false,
        onText: 'Yes',
        offText: 'No',
        errors: '',
        required: false,
        listeners: {},
        resetSources: [],
        toggleListeners: {},
        showelement: true,
        fireInputEvent: false,
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
            this.elvalue = false;
            this.errors = '';
        },
        processChange(status) {
            console.log('status: '+ status);
            this.elvalue = status;
            if (this.fireInputEvent) {
                $dispatch('eaforminputevent', {source: '{{$name}}', value: this.elvalue});
            }
        }
    }"

    x-init="
        @if (isset($_current_values[$name]))
            elvalue = {{ $_current_values[$name] }}
        @elseif (isset($_old[$name]) && (in_array($_old[$name], [0, false, 'false', 'no', 'False', 'No'])))
            elvalue = true;
        @endif
        @if (!$show)
            showelement =  false;
        @endif
        @if (isset($displayText) && is_array($displayText))
            displayText = true;
            onText = '{{$displayText[0]}}';
            offText = '{{$displayText[1];}}'
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
            elvalue = '{{$_old[$name]}}';
        @endif
        @if ($fire_input_event)
            fireInputEvent = true;
        @endif
    "
    @class([
        'relative',
        'form-control',
        $wclass,
        'flex flex-row' => $label_position != 'top'
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
    x-show="showelement"
    >
    {{-- @if ($label_position != 'float') --}}
    {{-- <label for="{{$name}}" @class([
            'label py-0 my-0 justify-start' => true,
            'w-36' => $label_position == 'side',
            'mr-2' => $label_position == 'float',
        ])>
        <span class="label-text">{{$label}}</span>@if (isset($properties['required']) && $properties['required'])
        &nbsp;<span class="text-warning">*</span>@endif
    </label> --}}
    {{-- @endif --}}
    <div @class([
            'flex' => true,
            'flex-grow' => $label_position == 'side',
            'w-full' => $label_position == 'top',
        ]) >

        <label for="{{$name}}" @class([
            'label py-0 my-0 justify-start' => true,
            'w-36' => $label_position == 'side',
            'mr-2' => $label_position == 'float',
        ])>
        <span class="label-text">{{$label}}</span>@if (isset($properties['required']) && $properties['required'])
        &nbsp;<span class="text-warning">*</span>@endif
        </label>
        <input
            id="{{$name}}" x-model="elvalue" type="checkbox"
            @if ($label_position == 'float')
            placeholder="' '"
            @else
            placeholder="{{$placeholder ?? ''}}"
            @endif
            class="peer checkbox checkbox-primary @if (isset($element['toggle']) && $element['toggle'])
                toggle
            @endif"
            :class="errors.length == 0 || 'border-error  border-opacity-50'"
            :checked="elvalue == true"
            @foreach ($properties as $prop => $val)
                @if (!is_bool($val))
                    {{$prop}}="{{$val}}"
                @elseif ($val)
                    {{$prop}}
                @endif
            @endforeach
            @change="processChange($el.checked);"
            />&nbsp;&nbsp;&nbsp;
            <span x-show="displayText" x-text="elvalue ? onText : offText"></span>
            <input type="hidden" name="{{$name}}" x-model="elvalue">

        {{-- @if ($label_position == 'float')
        <label for="{{$name}}" class="absolute text-warning peer-checked:text-base-content duration-300 transform -translate-y-4 scale-90 top-2 left-2 z-10 origin-[0] bg-base-100 px-2 peer-focus:px-2 peer-focus:text-warning [&:not(peer-checked)]:scale-100 [&:not(peer-checked)]:-translate-y-1/2 [&:not(peer-checked)]:top-1/2 peer-focus:top-2 peer-focus:scale-90 peer-focus:-translate-y-4 transition-all">

            <span>{{$label}}</span>@if (isset($properties['required']) && $properties['required'])
            &nbsp;<span class="text-warning">*</span>
            @endif
        </label>
        @endif --}}

        <x:easyadmin::partials.errortext />
    </div>
</div>

@endif
