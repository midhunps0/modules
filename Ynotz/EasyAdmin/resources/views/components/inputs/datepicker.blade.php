@props([
    'element',
    '_old' => [],
    '_current_values' => [],
    'xerrors' => [],
    'label_position' => 'top'
    ])
@php
    $type = $element['input_type'];
    $name = $element['key'];
    $startYear = $element['start_year'];
    $endYear = $element['end_year'];
    $dateFormat = $element['date_format'];
    $authorised = $element['authorised'];
    $label = $element['label'];
    $width = $element['width'] ?? 'full';
    $placeholder = $element['placeholder'] ?? null;
    $wrapper_styles = $element['wrapper_styles'] ?? null;
    $input_styles = $element['input_styles'] ?? null;
    $properties = $element['properties'] ?? [];
    $fire_input_event = $element['fire_input_event'] ?? false;
    $update_on_events = $element['update_on_events'] ?? null;
    $reset_on_events = $element['reset_on_events'] ?? null;
    $toggle_on_events = $element['toggle_on_events'] ?? null;
    $show = $element['show'] ?? true;
    $wclass = 'w-64';
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
    $ulid = Illuminate\Support\Str::ulid();
@endphp
@if ($authorised)
    <div class="{{$wclass}}">
        <div x-data="{
                MONTH_NAMES: [
                    'January',
                    'February',
                    'March',
                    'April',
                    'May',
                    'June',
                    'July',
                    'August',
                    'September',
                    'October',
                    'November',
                    'December',
                ],
                MONTH_SHORT_NAMES: [
                    'Jan',
                    'Feb',
                    'Mar',
                    'Apr',
                    'May',
                    'Jun',
                    'Jul',
                    'Aug',
                    'Sep',
                    'Oct',
                    'Nov',
                    'Dec',
                ],
                DAYS: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
                showDatepicker: false,
                datepickerValue: '',
                selectedDate: '',
                dateFormat: 'DD-MM-YYYY',
                startYear: 2000,
                endYear: 2050,
                yearsRange: [],
                month: '',
                year: '',
                no_of_days: [],
                blankdays: [],
                fireInputEvent: false,
                listeners: {},
                resetSources: [],
                toggleListeners: {},
                showelement: true,
                displaybox: null,
                position: 'bottom',
                errors: '',
                initDate() {
                    let today;
                    if (this.selectedDate) {
                        console.log('date chosen');
                        console.log(this.selectedDate);
                        today = new Date(Date.parse(this.selectedDate));
                    } else {
                        console.log('date NOT chosen');
                        today = new Date();
                        console.log(today);
                    }
                    this.month = today.getMonth();
                    this.year = today.getFullYear();
                    if (this.selectedDate) {
                        this.datepickerValue = this.formatDateForDisplay(
                            today
                        );
                    } else {
                        this.datepickerValue = '';
                    }
                    for(i = this.startYear; i <= this.endYear; i++) {
                        this.yearsRange.push(i);
                    }
                },
                formatDateForDisplay(date) {
                    let formattedDay = this.DAYS[date.getDay()];
                    let formattedDate = ('0' + date.getDate()).slice(
                        -2
                    ); // appends 0 (zero) in single digit date
                    let formattedMonth = this.MONTH_NAMES[date.getMonth()];
                    let formattedMonthShortName =
                        this.MONTH_SHORT_NAMES[date.getMonth()];
                    let formattedMonthInNumber = (
                        '0' +
                        (parseInt(date.getMonth()) + 1)
                    ).slice(-2);
                    let formattedYear = date.getFullYear();
                    if (this.dateFormat === 'DD-MM-YYYY') {
                        return `${formattedDate}-${formattedMonthInNumber}-${formattedYear}`; // 02-04-2021
                    }
                    if (this.dateFormat === 'YYYY-MM-DD') {
                        return `${formattedYear}-${formattedMonthInNumber}-${formattedDate}`; // 2021-04-02
                    }
                    if (this.dateFormat === 'D d M, Y') {
                        return `${formattedDay} ${formattedDate} ${formattedMonthShortName} ${formattedYear}`; // Tue 02 Mar 2021
                    }
                    return `${formattedDay} ${formattedDate} ${formattedMonth} ${formattedYear}`;
                },
                isSelectedDate(date) {
                    const d = new Date(this.year, this.month, date);
                    return this.datepickerValue ===
                        this.formatDateForDisplay(d) ?
                        true :
                        false;
                },
                isToday(date) {
                    const today = new Date();
                    const d = new Date(this.year, this.month, date);
                    return today.toDateString() === d.toDateString() ?
                        true :
                        false;
                },
                getDateValue(date) {
                    let selectedDate = new Date(
                        this.year,
                        this.month,
                        date
                    );
                    this.datepickerValue = this.formatDateForDisplay(
                        selectedDate
                    );
                    let m = String(this.month+1);
                    m = m.lenght == 1 ? '0'+m : m;
                    let d = String(date);
                    d = d.lenght == 1 ? '0'+d : d;
                    this.selectedDate = this.year + '-' + m + '-' + d;
                    if (this.fireInputEvent) {
                        $dispatch('eaforminputevent', {source: '{{$name}}', value: this.datepickerValue});
                    }
                    // this.$refs.date.value = selectedDate.getFullYear() + '-'' + ('0' + formattedMonthInNumber).slice(-2) + '-'' + ('0' + selectedDate.getDate()).slice(-2);
                    this.isSelectedDate(date);
                    this.showDatepicker = false;
                },
                getNoOfDays() {
                    let daysInMonth = new Date(
                        this.year,
                        this.month + 1,
                        0
                    ).getDate();
                    // find where to start calendar day of week
                    let dayOfWeek = new Date(
                        this.year,
                        this.month
                    ).getDay();
                    let blankdaysArray = [];
                    for (var i = 1; i <= dayOfWeek; i++) {
                        blankdaysArray.push(i);
                    }
                    let daysArray = [];
                    for (var i = 1; i <= daysInMonth; i++) {
                        daysArray.push(i);
                    }
                    this.blankdays = blankdaysArray;
                    this.no_of_days = daysArray;
                },
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
                                let data = JSON.parse(r.results);
                                this.month = data.month;
                                this.year = data.year;
                                this.selectedDate = r.date;
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
                    this.selectedDate = '';
                    this.datepickerValue = '';
                    this.errors = '';
                },
                setCalendarMonth(m) {
                    this.month = m;
                    this.getNoOfDays();
                },
                setCalendarYear(y) {
                    this.year = y;
                    this.getNoOfDays();
                },
                calendarFromDate() {
                    let date;

                    if (this.selectedDate) {
                        date = new Date(Date.parse(this.selectedDate));
                    } else {
                        date = new Date();
                    }
                    this.month = date.getMonth();
                    this.year = date.getFullYear();
                    this.getNoOfDays();
                },
                setPosition() {
                    let x = window.innerHeight - this.displaybox.getBoundingClientRect().bottom;
                    this.position = x < 200 ? 'top' : 'bottom';
                }
            }" x-init="
                displaybox = $el;
                selectedDate = '{{$_old[$name] ?? ''}}';
                startYear = {{$startYear}};
                endYear = {{$endYear}};
                dateFormat = '{{$dateFormat}}';
                initDate();
                getNoOfDays();

                @if($fire_input_event)
                    fireInputEvent = true;
                @endif

                @if (isset($update_on_events))
                    @foreach ($update_on_events as $source => $api)
                        listeners.{{$source}} = {
                            serviceclass: @if (isset($api[0])) '{{$api[0]}}' @else null @endif,
                            method: @if (isset($api[1])) '{{$api[1]}}' @else null @endif,
                        };
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
            @if (isset($update_on_events) || isset($toggle_on_events))
            @eaforminputevent.window="@if (isset($update_on_events))updateOnEvent($event.detail.source, $event.detail.value);@endif @if(isset($toggle_on_events))toggleOnEvent($event.detail.source, $event.detail.value);@endif"
            @endif
            @if (isset($reset_on_events) && count($reset_on_events) > 0)
            @eaforminputevent.window="resetOnEvent($event.detail);"
            @endif

            @class([
                'relative',
                'form-control',
                $wclass,
                // 'my-4' => $label_position != 'side',
                'flex flex-row' => $label_position == 'side'
            ])

            x-show="showelement"
            x-cloak>
            @if ($label_position != 'float')
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
                <div>
                    {{-- <label for="datepicker" class="font-bold mb-1 text-base-content block">Select Date</label> --}}
                    <div class="relative">
                        <input type="hidden" name="{{ $name }}" x-ref="date" :value="datepickerValue" />
                        <input type="text" id="di-{{$ulid}}"

                            @if ($label_position == 'float')
                            placeholder=" "
                            @else
                            placeholder="{{$placeholder ?? ''}}"
                            @endif
                            x-on:click="calendarFromDate(); setPosition(); showDatepicker = !showDatepicker;" x-model="datepickerValue"
                            x-on:keydown.escape="showDatepicker = false;"
                            class="peer w-full pl-4 pr-10 py-3 leading-none rounded-lg shadow-sm bg-base-100 text-base-content font-medium input input-bordered"
                            readonly />
                        @if ($label_position == 'float')
                        <label x-on:click="document.getElementById('di-{{$ulid}}').click(); document.getElementById('di-{{$ulid}}').focus();" class="absolute text-warning peer-placeholder-shown:text-base-content duration-300 transform -translate-y-4 scale-90 top-2 left-2 z-10 origin-[0] bg-base-100 px-2 peer-focus:px-2 peer-focus:text-warning peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-90 peer-focus:-translate-y-4 transition-all">
                            {{-- {{$label}} --}}
                            <span>{{$label}}</span>@if (isset($properties['required']) && $properties['required'])
                            &nbsp;<span class="text-warning">*</span>
                            @endif
                        </label>
                        @endif
                        <div x-show="datepickerValue != ''" x-on:click.prevent.stop="reset();" class="absolute top-0 right-6 z-20 text-error text-opacity-50  px-3 py-2">
                            <x-easyadmin::display.icon icon="easyadmin::icons.delete" height="h-6" width="w-6" />
                        </div>
                        <div x-on:click="calendarFromDate(); setPosition(); showDatepicker = !showDatepicker;" class="absolute top-0 right-0 px-3 py-2">
                            <svg class="h-6 w-6 text-base-content text-opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>

                        <!-- <div x-text="no_of_days.length"></div>
                          <div x-text="32 - new Date(year, month, 32).getDate()"></div>
                          <div x-text="new Date(year, month).getDay()"></div> -->
                        {{-- <div x-show.transition="showDatepicker" class="fixed w-screen h-screen bg-base-200 bg-opacity-50 flex flex-row items-center justify-center top-0 left-0 z-50"> --}}
                            <div x-show.transition="showDatepicker" class="bg-base-100 mt-12 border border-base-content border-opacity-20 rounded-lg shadow p-4 absolute left-0 z-50"
                                :class="position == 'bottom' ? 'top-0' : 'bottom-12'"
                                style="width: 17rem"
                                @click.away="showDatepicker = false">
                                <div class="flex justify-between items-center mb-2">
                                    <div class="w-full flex flex-row justify-between">
                                        <div class="relative w-3/5 p-0 bg-base-100 flex flex-row items-center">
                                            <div x-data="{showlist: false}">
                                                <button type="button" @click="showlist = true;" class="w-full">
                                                    <span x-text="MONTH_NAMES[month]" class="text-lg font-bold text-base-content"></span>
                                                    <x-easyadmin::display.icon class="text-sm"  icon="easyadmin::icons.chevron_down" />
                                                </button>
                                                <div @click.outside="showlist = false;" x-show="showlist" x-transition class="w-full absolute top-6 left-0 flex flex-row flex-wrap bg-base-200 p-1 items-center justify-evenly shadow-lg rounded-md">
                                                    <template x-for="(m, index) in MONTH_SHORT_NAMES">
                                                        <button type="button" @click="setCalendarMonth(index); showlist = false;" class="w-1/4 m-1 p-1 bg-base-100 border border-base-100 rounded-md">
                                                            <span x-text="m"></span>
                                                        </button>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>

                                        <div x-data="{showlist: false}" class="relative w-1/3 p-0 bg-base-100">
                                            <div x-data="{showlist: false}">
                                                <button type="button" @click="showlist = true;" class="w-full">
                                                    <span x-text="year" class="text-lg font-bold text-base-content"></span>
                                                    <x-easyadmin::display.icon class="text-sm"  icon="easyadmin::icons.chevron_down" />
                                                </button>
                                                <div @click.outside="showlist = false;" x-show="showlist" x-transition class="w-60 max-h-52 overflow-y-scroll absolute top-6 right-0 flex flex-row flex-wrap bg-base-200 p-1 items-center justify-evenly shadow-lg rounded-md">
                                                    <template x-for="y in yearsRange">
                                                        <button type="button" @click="setCalendarYear(y); showlist = false;" class="w-1/4 m-1 p-1 bg-base-100 border border-base-100 rounded-md">
                                                            <span x-text="y"></span>
                                                        </button>
                                                    </template>
                                                </div>
                                            </div>

                                            {{-- <span @click="showlist = true;" x-text="year" class="ml-1 text-lg font-bold text-base-content"></span>&nbsp;
                                            <x-easyadmin::display.icon class="text-sm" @click="showlist = true;" icon="easyadmin::icons.edit" />
                                            <select @click.outside="showlist = false;" x-show="showlist" @change="getNoOfDays(); showlist = false;" x-model.number="year" class="absolute top-0 left-0 z-20 w-full bg-base-100 text-xs font-bold border-none outline-none">
                                                <template x-for="y in yearsRange">
                                                    <option :value="y" :selected="y == year" x-text="y"></option>
                                                </template>
                                            </select> --}}
                                        </div>

                                    </div>
                                    {{-- <div>
                                        <button type="button"
                                            class="focus:outline-none focus:shadow-outline transition ease-in-out duration-100 inline-flex cursor-pointer hover:bg-gray-100 p-1 rounded-full"
                                            @click="if (month == 0) {
                                                    year--;
                                                    month = 12;
                                                } month--; getNoOfDays()">
                                            <svg class="h-6 w-6 text-base-content inline-flex" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 19l-7-7 7-7" />
                                            </svg>
                                        </button>
                                        <button type="button"
                                            class="focus:outline-none focus:shadow-outline transition ease-in-out duration-100 inline-flex cursor-pointer hover:bg-gray-100 p-1 rounded-full"
                                            @click="if (month == 11) {
                                                    month = 0;
                                                    year++;
                                                } else {
                                                    month++;
                                                } getNoOfDays()">
                                            <svg class="h-6 w-6 text-base-content inline-flex" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5l7 7-7 7" />
                                            </svg>
                                        </button>
                                    </div> --}}
                                </div>

                                <div class="flex flex-wrap mb-3 -mx-1">
                                    <template x-for="(day, index) in DAYS" :key="index">
                                        <div style="width: 14.26%" class="px-0.5">
                                            <div x-text="day" class="text-base-content font-medium text-center text-xs"></div>
                                        </div>
                                    </template>
                                </div>

                                <div class="flex flex-wrap -mx-1">
                                    <template x-for="blankday in blankdays">
                                        <div style="width: 14.28%"
                                            class="text-center border p-1 border-transparent text-sm"></div>
                                    </template>
                                    <template x-for="(date, dateIndex) in no_of_days" :key="dateIndex">
                                        <div style="width: 14.28%" class="px-1 mb-1">
                                            <div @click="getDateValue(date)" x-text="date"
                                                class="cursor-pointer text-center text-sm rounded-full leading-loose transition ease-in-out duration-100"
                                                :class="{
                                                    'bg-base-200': isToday(date) == true,
                                                    'text-base-content hover:bg-primary': isToday(date) == false &&
                                                        isSelectedDate(date) == false,
                                                    'bg-primary text-base-content': isSelectedDate(date) ==
                                                        true
                                                }">
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        {{-- </div> --}}
                    </div>
                </div>


                <x:easyadmin::partials.errortext />
            </div>
        </div>


    </div>
@endif
