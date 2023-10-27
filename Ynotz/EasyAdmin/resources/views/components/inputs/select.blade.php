@props([
    'element',
    '_old' => [],
    '_current_values' => [],
    'xerrors' => collect([]),
    'label_position' => 'top',
    'model_key' => null
])
@php
    $name = $element['key'];
    $label = $element['label'];
    $options = $element['options'];
    $none_selected = $element['none_selected'] ?? 'Select';
    $options_type = $element['options_type'] ?? 'key_value';
    $options_id_key = $element['options_id_key'] ?? 'id';
    $options_text_key = $element['options_text_key'] ?? 'name';
    $options_src = $element['options_src'] ?? null;
    $fetch_url = $element['fetch_url'] ?? null;
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
    $authorised = $element['authorised'] ?? true;

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
//        values: [],
        keyname: '',
        oldVals: [],
        select_options: [],
        search: '',
        errors: '',
        multiple: false,
        search: '',
        dependedId: null,
        fetchUrl: '',
        options: [],
        optionsType: 'collection',
        selected: [],
        selectedVals: [],
        show: false,
        selId: '',
        fireInputEvent: false,
        updateSources: [],
        resetSources: [],
        toggleListeners: {},
        showelement: true,
        fetchOptions(multiple = false) {
            let params = {
                'depended_id': this.dependedId
            };
            axios.get(
                this.fetchUrl,
                {
                    params: params,
                    paramsSerializer: {
                      indexes: multiple
                    }
                }
            ).then((r) => {
                this.select_options = [];
                r.data.results.forEach((item) => {
                    this.select_options.push({key: item.{{$options_id_key}}, text: item.{{$options_text_key}}});
                });

                this.initOptions(this.select_options)
            }).catch((e) => {
                console.log(e);
            });
        },
        open() { this.show = true; this.focusOnFirstOption();},
        close() { this.show = false; this.search = ''; },
        isOpen() { return this.show === true },
        select(val, event) {
            let theoption = this.options.filter((op) => {
                return op.value == val;
            })[0];
            {{-- if (!theoption.selected) { --}}

            if (!this.multiple) {
                this.options.forEach((op) => {
                    op.selected = op.value == val;
                });
                this.selected = [];
                this.selectedVals = [];
            } else {
                this.options.forEach((op) => {
                    if (op.value == val) {
                        op.selected = true;
                    }
                });
            }
            this.selected.push(theoption);
            this.selectedVals.push(val);
            {{-- } else {
                this.options.forEach((op) => {
                    if (op.value == val) {
                        op.selected = false;
                    }
                });
                this.selected = this.selected.filter((item) => {
                    return item.value != val;
                });
                this.selectedVals = this.selectedVals.filter((item) => {
                    return item != val;
                });
            } --}}
            {{-- this.focusOnList(); --}}
            {{-- this.search = ''; --}}
            if (this.fireInputEvent) {
                $dispatch('eaforminputevent', {source: '{{$name}}', value: JSON.parse(JSON.stringify(this.selectedVals)), multiple: this.multiple});
            }
            if (!this.multiple) {
                this.close();
            }
        },
        remove(index, val) {
            let theoption = this.selected.filter((op) => {
                return op.value == val;
            })[0];
            this.options = this.options.map((op) => {
                if (op.value == val) {
                    op.selected = false;
                }
                return op;
            });
            this.selected = this.selected.filter((op) => {
                return op.value != val;
            });
            this.selectedVals = this.selectedVals.filter((v) => {
                return v != val;
            });
            if (this.fireInputEvent) {
                $dispatch('eaforminputevent', {source: '{{$name}}', value: JSON.parse(JSON.stringify(this.selectedVals)), multiple: this.multiple});
            }
        },
        initOptions(options) {
            {{-- let intvalues = this.selectedVals.map((v) => {
                return parseInt(v);
            }); --}}
            console.log('options (select_options) inside initOptions');
            console.log(options);
            this.options = [];
            for (let i = 0; i < options.length; i++) {
                let op = {
                    value: options[i].key,
                    text: options[i].text,
                };
                Object.keys(options[i]).forEach((k) => {
                  if (k != 'key' && k != 'text') {
                    op[k] = options[i][k];
                  }
                });
                this.options.push(op);
                {{-- this.options.push({
                    value: options[i].key,
                    text: options[i].text,
                    selected: intvalues.includes(parseInt(options[i].key))
                }); --}}
            }
        },
        focusOnFirstItem() {
            $nextTick(() => {
                let items = document.getElementById('multiselect-items').children;
                setTimeout(() => {
                    if (items.length > 1) {
                        items[1].focus();
                    }
                }, 100);

            });
        },
        focusOnFirstOption() {
            $nextTick(() => {
                let items = document.getElementById('multiselect-items').children;
                setTimeout(() => {
                    if (items.length > 1) {
                        items[1].focus();
                    }
                }, 100);

            });
        },
        focusOnList() {
            $nextTick(() => {
                let item = document.getElementById('slinput');
                setTimeout(() => {
                    if (item != null && item != undefined) {
                        item.focus();
                    }
                }, 100);

            });
        },
        updateOnEvent(detail) {
            if(this.updateSources.includes(detail.source)) {
                this.reset();
                if (!detail.multiple) {
                    this.dependedId = typeof detail.value[0] != 'undefined' ? detail.value[0] : null;
                    if (this.dependedId != null) {
                        this.fetchOptions();
                    }
                } else {
                    this.dependedId = detail.value;
                    if (this.dependedId.length > 0) {
                        this.fetchOptions(detail.multiple);
                    }
                }
                //this.dependedId = detail.value;
                //this.fetchOptions();
            }
        },
        resetOnEvent(detail) {
            if(this.resetSources.includes(detail.source)) {
                this.reset();
            }
        },
        reset() {
            {{-- this.select_options = []; --}}
            this.dependedId = null;
            this.selected = [];
            this.selectedVals = [];
            this.options = [];
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
        isSelected(key) {
            if (this.optionsType != 'value_only') {
                let x = this.selectedVals.filter((s) => {
                    return parseInt(key) == parseInt(s);
                });
                if (x.length == 0) {
                    x = this.selectedVals.filter((s) => {
                        return key == s;
                    });
                }
                return x.length > 0;
            }

            return this.selectedVals.includes(key);
        }
    }"
    @class([
        'relative',
        'form-control',
        $wclass,
        'my-6 flex flex-row' => $label_position == 'side',
    ])

    x-init="
        keyname = '{{$name}}';
        optionsType = '{{$options_type}}'
        @if (!$show)
            showelement =  false;
        @endif
        let x = null;
        @if(isset($_old[$name]))
            @if(isset($properties['multiple']) && $properties['multiple'])
                @foreach ($_old[$name] as $val)
                @if ($options_type == 'value_only' || $options_type == 'key_value')
                    selectedVals.push('{{$_old[$name]}}');
                @else
                    x = JSON.parse('{{$val}}');
                    selectedVals.push(x['{{$options_id_key}}']);
                @endif
                @endforeach
            @else
                @if ($options_type == 'value_only')
                    selectedVals = ['{{$_old[$name]}}'];
                @elseif ($options_type == 'key_value')
                    selectedVals = ['{{$_old[$name]}}'];
                @else
                    x = JSON.parse('{{$_old[$name]}}');
                    selectedVals = [x['{{$options_id_key}}']];
                @endif
            @endif
        @endif
        @if (isset($fetch_url))
            fetchUrl = '{{$fetch_url}}';
        @endif
        ops = JSON.parse('{{json_encode($options)}}');
        {{-- select_options.push({key: '', text: '{{$none_selected}}'}); --}}

        @if ($options_type == 'key_value')
            Object.keys(ops).forEach((key) => {
                select_options.push({key: key, text: ops[key]});
            });
        @elseif ($options_type == 'value_only')
        Object.values(ops).forEach((op) => {
                select_options.push({key: op, text: op });
            });

        {{-- if (keyname == 'gender' || keyname == 'marital_status') {
            console.log('select_options');
            console.log(select_options);
        } --}}
        @elseif ($options_type == 'collection')
            {{-- ops.forEach((op) => {
                select_options.push({key: op.{{$options_id_key}}, text: op.{{$options_text_key}} });
            }); --}}
            @if (!isset($options_display_keys))
            Object.keys(ops).forEach((k) => {
                    select_options.push({key: ops[k].{{$options_id_key}}, text: ops[k].{{$options_text_key}} });
                });
            @else
                ops.forEach((op) => {
                    select_options.push({
                        key: op.{{$options_id_key}},
                        text: op.{{$options_text_key}},
                        @foreach ($option_display_keys as $key)
                        @if ($key != $options_text_key)
                        {{$key}}: op.{{$key}},
                        @endif
                        @endforeach
                    });
                });
            @endif
        @endif
        console.log('select_options: {{$name}}');
        console.log(select_options);
        @if ($xerrors->has($name))
            ers = {{json_encode($xerrors->get($name))}};
            errors = ers.reduce((r, e) => {
                return r + ' ' + e;
            }, '').trim();
        @endif
        @if (isset($properties['required']) && $properties['required'])
            required = true;
        @endif
        selId = 'select-{{$name}}';
        initOptions(select_options);
        $nextTick(() => {
            @if ($options_type == 'value_only')
                for(i=0; i < options.length; i++) {
                    if (selectedVals.includes(options[i].value)) {
                        selected.push(options[i]);
                        {{-- selectedVals.push(options[i].value); --}}
                    }
                };
            @else
            if (selectedVals.length > 0) {
                let theVals = selectedVals.map((v) => {
                    return isNaN(v) ? v : parseInt(v);
                });
                for(i=0; i < options.length; i++) {
                    if (isNaN(options[i].value)) {
                        if (selectedVals.includes(options[i].value)) {
                            selected.push(options[i]);
                            {{-- selectedVals.push(options[i].value); --}}
                        }
                    } else {
                        if (selectedVals.includes(parseInt(options[i].value))) {
                            selected.push(options[i]);
                        }
                    }
                };
            }
            @endif
        });

        @if (isset($properties['multiple']) && $properties['multiple'])
            multiple = true;
        @endif

        @if($fire_input_event)
            fireInputEvent = true;
        @endif

        @if (isset($update_on_events))
            @foreach ($update_on_events as $source)
                updateSources.push('{{$source}}');
            @endforeach
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
        $watch('selectedVals', (v) => {
            console.log('selectedVals');
            console.log(v);
        })
    "
    @if (isset($reset_on_events) || isset($update_on_events))
    @eaforminputevent.window="resetOnEvent($event.detail); updateOnEvent($event.detail);"
    @endif
    @if (isset($toggle_on_events) && count($toggle_on_events) > 0)
    @eaforminputevent.window="toggleOnEvent($event.detail.source, $event.detail.value);"
    @endif
    @formerrors.window="
        errors = '';

        erArr = [];
        Object.keys($event.detail.errors).forEach((e) => {
            if (e.startsWith('{{$name}}')) {
                erArr.push(($event.detail.errors[e]).reduce((result, e) => {
                    if (result.length > 0) {
                        return result + ' ' + e;
                    } else {
                        return result + e;
                    }
                }));
            }
        });

        errors = erArr.reduce(
            (result, e) => {
                if (result.length > 0) {
                    return result + ' ' + e;
                } else {
                    return result + e;
                }
            },
            ''
        );
    "
    x-show="showelement"
    >
    @if ($label_position != 'float' && $label != '')
        <label @click="document.getElementById('{{$name}}-wrapper').click();" @class(['label', 'justify-start', 'w-36 min-w-36' => $label_position == 'side'])>
            <span class="label-text">{{ $label }}</span>@if (isset($properties['required']) && $properties['required'])
            &nbsp;<span class="text-warning">*</span>@endif
        </label>
    @endif
    <div @class([
            'flex-grow' => $label_position == 'side',
            'w-full' => $label_position != 'side',
        ])>

        <div class="w-full flex flex-col items-center">
            <div class="inline-block relative w-full">
                <div class="flex flex-col items-center relative">
                    <select @if(isset($model_key))x-model="{{$model_key}}" @endif tabindex="-1" id="select-{{$name}}" @if(isset($properties['multiple']) && $properties['multiple'])  x-model="selectedVals" name="{{ $name }}[]" @else x-model="selectedVals[0]" name="{{$name}}" @endif class="h-0 w-11/12 absolute -z-10 rounded-md left-1 top-4 overflow-hidden"
                        @foreach ($properties as $prop => $val)
                            @if (!is_bool($val))
                                {{ $prop }}="{{ $val }}"
                            @elseif ($val)
                                {{ $prop }}
                            @endif
                        @endforeach @if(isset($properties['multiple']) && $properties['multiple'])  multiple @endif>
                            <option value="">{{$none_selected}}</option>
                        <template x-for="op in select_options">
                            <option :value="op.key" x-text="op.text + ' : ' +op.key" :selected="isSelected(op.key)"></option>
                        </template>
                    </select>
                    <div x-on:click="open" @keypress="open()" class="w-full"
                    id="{{$name}}-wrapper"
                    tabindex="0">
                        <div class="p-1 input flex items-start border bg-base-100 rounded-lg h-auto"
                        :class="errors.length > 0 ? 'border-error border-opacity-50' : 'border-base-content border-opacity-20'">
                            <div class="flex flex-row flex-wrap w-full max-h-20 overflow-y-scroll">
                                <template x-for="(option,index) in selected" :key="option.value">
                                    <div
                                        class="flex justify-center items-center m-1 font-medium py-0 px-1 bg-base-100 rounded border border-base-300">
                                        <div class="text-xs font-normal leading-none max-w-full flex-initial" x-model="option.value" x-text="option.text"></div>&nbsp;
                                        <div class="flex flex-auto flex-row-reverse">
                                            <button x-on:click.stop="remove(index,option.value)">
                                                <svg class="fill-current h-4 w-4 text-base-content opacity-50" role="button" viewBox="0 0 20 20">
                                                    <path
                                                        d="M14.348,14.849c-0.469,0.469-1.229,0.469-1.697,0L10,11.819l-2.651,3.029c-0.469,0.469-1.229,0.469-1.697,0
                                                     c-0.469-0.469-0.469-1.229,0-1.697l2.758-3.15L5.651,6.849c-0.469-0.469-0.469-1.228,0-1.697s1.228-0.469,1.697,0L10,8.183
                                                     l2.651-3.031c0.469-0.469,1.228-0.469,1.697,0s0.469,1.229,0,1.697l-2.758,3.152l2.758,3.15
                                                     C14.817,13.62,14.817,14.38,14.348,14.849z" />
                                                </svg>

                                            </button>
                                        </div>
                                    </div>
                                </template>
                            </div>
                            <div
                                class="text-gray-300 w-8 py-1 pl-2 pr-1 border-l flex flex-row justify-center items-center border-base-200 bg-base-200 rounded-md">

                                <button type="button" x-on:click="isOpen() === true ? open() : close()"
                                    class="cursor-pointer w-6 h-6 text-gray-600">
                                    <span x-show="isOpen() === true">
                                        <x-easyadmin::display.icon icon="easyadmin::icons.chevron_up"/>
                                    </span>
                                    <span x-show="isOpen() === false">
                                        <x-easyadmin::display.icon icon="easyadmin::icons.chevron_down"/>
                                    </span>
                                </button>
                                {{-- <button type="button" x-show="isOpen() === false" @click="close();"
                                    class="cursor-pointer w-6 h-6 text-gray-600">
                                    <span x-show="!isOpen()">
                                        <x-easyadmin::display.icon icon="easyadmin::icons.chevron_down"/>
                                    </span>
                                </button> --}}
                            </div>
                        </div>
                    </div>
                    <div class="w-full px-4">
                        <div x-show.transition.origin.top="isOpen()"
                            class="absolute shadow top-100 bg-base-100 z-40 w-full left-0 rounded max-h-select"
                            x-on:click.away="close">
                            <div @keyup.prevent="if($event.key=='Escape'){close();}" class="flex flex-col w-full overflow-y-auto h-48 border border-base-200" id="multiselect-items">
                                {{-- <input id="slinput" x-model="search" @keyup="$dispatch('sldosearch', {searchstr: search});" type="text" placeholder="Search" class="input input-md border border-base-content border-opacity-20 bg-base-100"> --}}
                                <template x-for="(option,index) in options" :key="option.value"
                                    class="overflow-auto">
                                    <button @click.prevent.stop="false;" x-show="!option.selected" class="cursor-pointer w-full border-base-200 rounded-t border-b focus:outline-none focus:bg-base-200 hover:bg-base-200 js-btn"
                                        @click="select(option.value,$event)">
                                        <x-dynamic-component :component="$element['list_component']" />
                                        {{-- <div
                                            class="flex w-full items-center p-2 pl-2 border-transparent border-l-2 relative">
                                            <div class="w-full items-center flex justify-between">
                                                <div class="mx-2 leading-6" x-text="option.text"></div>
                                                <div x-show="option.selected">
                                                    <svg class="svg-icon" viewBox="0 0 20 20">
                                                        <path fill="none"
                                                            d="M7.197,16.963H7.195c-0.204,0-0.399-0.083-0.544-0.227l-6.039-6.082c-0.3-0.302-0.297-0.788,0.003-1.087
                                      C0.919,9.266,1.404,9.269,1.702,9.57l5.495,5.536L18.221,4.083c0.301-0.301,0.787-0.301,1.087,0c0.301,0.3,0.301,0.787,0,1.087
                                      L7.741,16.738C7.596,16.882,7.401,16.963,7.197,16.963z">
                                                        </path>
                                                    </svg>
                                                </div>
                                            </div>
                                        </div> --}}
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if ($label_position == 'float' && $label != '')
            <label  @click="document.getElementById('{{$name}}-wrapper').click();"
                class="absolute duration-300 bg-base-100 px-2 transition-all left-2"
                :class="selectedVals.length > 0 ? 'text-warning transform -translate-y-4 scale-90 top-2 z-10 origin-[0]' : 'transform translate-y-2 top-2'"
                >
                <span>{{ $label }}</span>@if (isset($properties['required']) && $properties['required'])
                &nbsp;<span class="text-warning">*</span>@endif
            </label>
        @endif
        <x:easyadmin::partials.errortext />
    </div>
</div>
@endif
