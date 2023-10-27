@props(['options'=> [], 'fieldname', 'selectedoption' => '', 'key_type' => 'numeric'])
<div x-data="{
    visible: false,
    val: '',
    op: ''
    }"
    x-init="
    visible = false;
    let type = '{{$key_type}}';
    if (type == 'numeric') {
        op = 'eq';
    } else {
        op = 'is';
    }
    val = '{{$selectedoption}}';
            visible = val != '';
    "
    class="form-control w-full max-w-full h-full flex flex-row justify-end items-center absolute top-0 left-0 flex-grow flex-shrink">
    <div x-show="visible" class="absolute z-10 flex-grow max-w-full flex flex-row right-10">
        <select
        {{-- x-data --}}
            {{-- x-init="
            val = '{{$selectedoption}}';
            visible = val != '';
            console.log('val');
            console.log(val);
            " --}}
            @change.stop.prevent="$dispatch('spotfilter', {data: { {{$fieldname}}: op+'::'+val}});"
            x-model="val" class="select select-bordered select-sm max-w-xs py-0 m-1 flex-grow"
            :class="val == '' || 'text-accent'">
            <option value="">All</option>
            @foreach ($options as $value => $text)
                <option value="{{ $value }}"
                @if (isset($item['selected']) && $item['selected'])
                    {{'selected'}}
                @endif
                >
                    {{ $text }}
                </option>
            @endforeach
        </select>
        {{-- <button
        @click.prevent.stop="visible = false;"
        class="btn btn-sm bg-error border-none focus:outline-primary-focus text-base-content hover:bg-base-100"
        ><x-easyadmin::display.icon icon="icons.close" height="h-3" width="w-3"/></button> --}}
    </div>
    <button x-show="!visible" class="btn btn-sm bg-inherit border-none focus:outline-primary-focus text-base-content hover:bg-base-100 opacity-50" @click.prevent.stop="visible=true;">
        <x-easyadmin::display.icon icon="easyadmin::icons.filter" height="h-6" width="w-6"/>
    </button>
    <button x-show="visible"
        @click.prevent.stop="visible = false; val = ''; $dispatch('spotfilter', {data: { {{$fieldname}}: op+'::'+val}});"
        class="btn btn-sm btn-error border-none focus:outline-primary-focus text-base-100 hover:bg-opacity-80 z-20"
        ><x-easyadmin::display.icon icon="easyadmin::icons.close" height="h-3" width="w-3"/>
    </button>
</div>
