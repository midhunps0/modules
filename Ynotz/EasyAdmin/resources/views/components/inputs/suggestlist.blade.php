@props([
    'element',
    '_old' => [],
    // '_current_values' => [],
    'xerrors' => [],
    'label_position' => 'top',
])

@php
    $name = $element['key'];
    $label = $element['label'];
    $options_type = $element['options_type'] ?? 'key_value';
    $options_id_key = $element['options_id_key'] ?? 'id';
    $options_text_key = $element['options_text_key'] ?? 'name';
    $options_src = $element['options_src'];
    $fetch_url = $element['fetch_url'];
    $width = $element['width'] ?? 'full';
    $none_selected = $element['none_selected'];
    $placeholder = $element["placeholder"] ?? null;
    $wrapper_styles = $element["wrapper_styles"] ?? null;
    $input_styles = $element["input_styles"] ?? null;
    $properties = $element['properties'] ?? [];
    $fire_input_event = $element['fire_input_event'] ?? false;
    $update_on_events = $element['update_on_events'] ?? null;
    $reset_on_events = $element['reset_on_events'] ?? null;
    $toggle_on_events = $element['toggle_on_events'] ?? null;
    $show = $element['show'] ?? true;
    $authorised = $element['authorised'];

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
        keyname: null,
        oldVals: [],
        select_options: [],
        search: '',
        errors: '',
        multiple: false,
        search: '',
        fetchUrl: null,
        dependedId: null,
        options: [],
        optionsType: 'collection',
        optionsIdKey: null,
        optionsTextKey: null,
        optionsDisplayKeys: null,
        selected: [],
        selectedVals: [],
        show: false,
        selId: '',
        fireInputEvent: false,
        updateSources: [],
        resetSources: [],
        toggleListeners: {},
        showelement: true,
        fetchOptions(val, multiple = false) {
                let params = {
                    'search': val
                };
                if (this.dependedId != null) {
                    params.depended_id = this.dependedId;
                }
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
                    let ops = r.data.results;
                    console.log('ops');
                    console.log(ops);
                    if (this.optionsType == 'key_value') {
                        Object.keys(ops).forEach((key) => {
                            this.select_options.push({key: key, text: ops[key]});
                        });
                    } else if (this.optionsType == 'value_only') {
                        ops.forEach((op) => {
                            this.select_options.push({key: op, text: op });
                        });
                    } else if (this.optionsType == 'collection') {
                        if (this.optionsDisplayKeys == null) {
                            ops.forEach((op) => {
                                this.select_options.push({key: op[this.optionsIdKey], text: op[this.optionsTextKey] });
                            });
                        } else {
                            ops.forEach((op) => {
                                let obj = {
                                    key: op[this.optionsIdKey],
                                    text: op[this.optionsTextKey],
                                };
                                this.optionDisplayKeys.foreach((k) => {
                                    if (k != this.optionsTextKey) {
                                        obj[k] = op[k];
                                    }
                                });
                                this.select_options.push(obj);
                                concole.log('this.select_options');
                                concole.log(this.select_options);
                            });
                        }
                    }
                    this.initOptions();
                }).catch((e) => {

                });
        },
        open() { this.show = true; this.focusOnList(); },
        close() { this.show = false; this.search = ''; },
        isOpen() { return this.show === true },
        select(val, event) {
            let theoption = this.options.filter((op) => {
                return op.value == val;
            })[0];
            if (!theoption.selected) {
                if (!this.multiple) {
                    this.selected = [];
                    this.selectedVals = [];
                }
                this.options.forEach((op) => {
                    if (op.value == val) {
                        op.selected = true;
                    } else if (!this.multiple) {
                        op.selected = false;
                    }
                });

                this.selected.push(theoption);
                this.selectedVals.push(val);
            } else {
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
            }

            if (this.fireInputEvent) {
                $dispatch('eaforminputevent', {source: this.keyname, value: JSON.parse(JSON.stringify(this.selectedVals)), multiple: this.multiple});
            }

            this.focusOnList();
            this.search = '';
            if (!this.multiple) {
                this.close();
            }
        },
        remove(index, val) {
            let theoption = this.selected.filter((op) => {
                return op.value == val;
            })[0];
            this.options.forEach((op) => {
                if(op.value == val) {
                    op.selected = false;
                }
            });
            /* this.options = this.options.filter((op) => {
                return op.value != val;
            }); */
            this.selected = this.selected.filter((op) => {
                return op.value != val;
            });
            this.selectedVals = this.selectedVals.filter((v) => {
                return v != val;
            });

            if (this.fireInputEvent) {
                $dispatch('eaforminputevent', {source: this.keyname, value: JSON.parse(JSON.stringify(this.selectedVals)), multiple: this.multiple});
            }
        },
        initOptions() {
            console.log('inside init: '+this.keyname);
            console.log(this.selectedVals);
            console.log(this.select_options);
            console.log(this.options);
            let intvalues = this.selectedVals.map((v) => {
                return isNaN(v) ? v :parseInt(v);
            });
            this.options = [];
            for (let i = 0; i < this.select_options.length; i++) {
                let op = {
                    value: this.select_options[i].key,
                    text: this.select_options[i].text,
                };
                Object.keys(this.select_options[i]).forEach((k) => {
                  if (k != 'key' && k != 'text') {
                    op[k] = this.select_options[i][k];
                  }
                });
                this.options.push(op);
                /* this.options.push({
                    value: this.select_options[i].key,
                    text: this.select_options[i].text,
                    selected: intvalues.includes(parseInt(this.select_options[i].key))
                }); */
            }
            if (this.selectedVals.length > 0) {
                for(i=0; i < this.options.length; i++) {
                    if (isNaN(this.options[i].value)) {
                        if (intvalues.includes(this.options[i].value)) {
                            this.selected.push(this.options[i]);
                        }
                    } else {
                        if (intvalues.includes(parseInt(this.options[i].value))) {
                            this.selected.push(this.options[i]);
                        }
                    }
                };
            }
            console.log('init finished: '+this.keyname);
            console.log(this.selectedVals);
            console.log(this.select_options);
            console.log(this.options);
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
        updateOnEvent(detail) { console.log('update event captured: '+this.keyname); console.log(this.updateSources); console.log(detail);
            if(this.updateSources.includes(detail.source)) {
                this.reset();
                if (!detail.multiple) {
                    this.dependedId = detail.value[0];
                    this.fetchOptions('');
                } else {
                    this.dependedId = detail.value;
                    if (this.dependedId.length > 0) {
                        this.fetchOptions('', detail.multiple);
                    }
                }
            }
        },
        resetOnEvent(detail) { console.log('reset event captured: '+this.keyname);
        console.log(this.keyname + ' resetSources');
        console.log(this.resetSources);
            if(this.resetSources.includes(detail.source)) {
                console.log('reset initiated..' + this.keyname);
                this.reset();
            }
        },
        reset() {
            this.selected = [];
            this.selectedVals = [];
            this.options = [];
            this.dependedId = null;
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
        }
    }"
    @class([
        'relative',
        'form-control',
        $wclass,
        'flex flex-row' => $label_position == 'side',
    ])
    x-init="
        keyname = '{{$name}}';
        @if (!$show)
            showelement =  false;
        @endif
        @if (isset($options_type))
            optionsType = '{{$options_type}}';
        @endif
        @if (isset($options_id_key))
            optionsIdKey = '{{$options_id_key}}';
        @endif
        @if (isset($options_text_key))
            optionsTextKey = '{{$options_text_key}}';
        @endif
        @if (isset($fetch_url))
            fetchUrl = '{{$fetch_url}}';
        @endif
        @if(isset($_old[$name]))
            @if(isset($properties['multiple']) && $properties['multiple'])
            {{-- {{dd($_old[$name])}} --}}
            selectedVals = {{Js::from($_old[$name])}};
            @else
            selectedVals = ['{{$_old[$name]}}'];
            @endif
        @elseif(isset($_old[$name]))
            @if(isset($properties['multiple']) && $properties['multiple'])
            selectedVals = [{{implode(',', $_old[$name])}}];
            @else
            selectedVals = [{{$_old[$name]}}];
            @endif
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
        selId = 'select-{{$name}}';
        $nextTick(() => {
            fetchOptions('');
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
    "
    @if (isset($reset_on_events) || isset($update_on_events))
    @eaforminputevent.window="resetOnEvent($event.detail);updateOnEvent($event.detail);"
    @endif
    @if (isset($toggle_on_events) && count($toggle_on_events) > 0)
    @eaforminputevent.window="toggleOnEvent($event.detail.source, $event.detail.value);"
    @endif
    @sldosearch.window="fetchOptions($event.detail.searchstr);"
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
    @if ($label_position != 'float')
        <label @click="document.getElementById('{{$name}}-wrapper').click();" @class(['label', 'justify-start', 'w-36' => $label_position == 'side'])>
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

                    <select tabindex="-1" id="select-{{$name}}" @if(isset($properties['multiple']) && $properties['multiple'])  x-model="selectedVals" name="{{ $name }}[]" @else x-model="selectedVals[0]" name="{{$name}}" @endif class="h-0 w-1/12 absolute -z-10 rounded-md left-1 top-4 right-4 overflow-hidden bg-transparent"
                        @foreach ($properties as $prop => $val)
                            @if (!is_bool($val))
                                {{ $prop }}="{{ $val }}"
                            @elseif ($val)
                                {{ $prop }}
                            @endif
                        @endforeach @if(isset($properties['multiple']) && $properties['multiple'])  multiple @endif>
                        <option value="">{{$none_selected}}</option>
                        <template x-for="op in select_options">
                            <option :value="op.key" x-text="op.text+' : '+op.key" :selected="selectedVals.includes(op.key)"></option>
                        </template>
                    </select>
                    <div id="{{$name}}-wrapper" x-on:click="open" class="w-full" tabindex="0" @keypress="open()">
                        <div class="p-1 input flex border bg-base-100 rounded-lg"
                        :class="errors.length > 0 ? 'border-error border-opacity-50' : 'border-base-content border-opacity-20'">
                            <div class="flex flex-auto flex-wrap">
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
                                class="text-gray-300 w-8 py-1 pl-2 pr-1 border-l flex items-center border-base-200 svelte-1l8159u">
                                <button type="button" x-on:click="isOpen() === true ? open() : close()"
                                    class="cursor-pointer w-6 h-6 text-gray-600">
                                    <span x-show="isOpen() === true">
                                        <x-easyadmin::display.icon icon="easyadmin::icons.chevron_up"/>
                                    </span>
                                    <span x-show="isOpen() === false">
                                        <x-easyadmin::display.icon icon="easyadmin::icons.chevron_down"/>
                                    </span>
                                </button>
                                {{-- <button type="button" x-show="isOpen() === true" x-on:click="open();"
                                    class="cursor-pointer w-6 h-6 text-gray-600">
                                    <x-easyadmin::display.icon icon="easyadmin::icons.zoom_out"/>
                                </button>
                                <button type="button" x-show="isOpen() === false" @click="close(); focusOnList();"
                                    class="cursor-pointer w-6 h-6 text-gray-600">
                                    <x-easyadmin::display.icon icon="easyadmin::icons.zoom_in"/>
                                </button> --}}
                            </div>
                        </div>
                    </div>
                    <div class="w-full px-4">
                        <div x-show.transition.origin.top="isOpen()"
                            class="absolute shadow top-100 bg-base-100 z-40 w-full left-0 rounded max-h-select"
                            x-on:click.away="close">
                            <div @keyup.prevent="if($event.key=='Escape'){close();}" class="flex flex-col w-full overflow-y-auto h-48 border border-base-200" id="multiselect-items">
                                <input id="slinput" x-model="search" @keyup="$dispatch('sldosearch', {searchstr: search});" type="text" placeholder="Search" class="input input-md border border-base-content border-opacity-20 bg-base-100">
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

        @if ($label_position == 'float')
            <label @click="document.getElementById('{{$name}}-wrapper').click();"
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
