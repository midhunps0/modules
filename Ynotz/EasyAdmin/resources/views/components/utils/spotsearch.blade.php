@props(['textval'=> '', 'condition' => null, 'textname', 'label'])

<div x-data="{
    textval: '',
    visible: false,
    condition: 'ct'
    }"
    x-init="
    textval = '{{$textval}}';
    visible = textval != '';
    condition = '{{ $condition ?? "ct" }}';
    "
    class="form-control w-full max-w-full h-full flex flex-row justify-end items-center absolute top-0 left-0 flex-grow flex-shrink">
    <div x-show="visible" class="absolute z-10 w-full max-w-full flex flex-row">
        <input type="text" x-ref="search_box" x-model="textval"
        @change.prevent.stop="if (textval.length > 0) {
                $dispatch('spotsearch', { {{$textname}}: condition+'::'+textval});
            }
        "
        @input.prevent.stop=""
        @keyup.prevent.stop="if($event.code == 'Escape') {
            textval='';
            visible=false;
            let vstr = null;
            if (textval && textval.length > 0) {
                vstr = condition+'::'+textval;
            }
            $dispatch('spotsearch', { {{$textname}}: vstr});
        }"
        @click.outside="visible=textval.length > 0;"
        placeholder="{{$label}}" class="input input-sm input-bordered text-accent w-full"/>
        <button type="button"
        @click.prevent.stop="
        if (textval=='') {
            visible=false;
        } else {
            textval='';
            visible=false;
            let vstr = null;
            if (textval && textval.length > 0) {
                vstr = condition+'::'+textval;
            }
            $dispatch('spotsearch', { {{$textname}}: vstr});
            {{-- $dispatch('spotsearch', { {{$textname}}: condition+'::'+textval}); --}}
        }"
        class="bg-error border-none rounded-tr-lg rounded-bl-sm inline-block h-4 w-4 p-0 absolute top-0 right-0"><x-easyadmin::display.icon icon="easyadmin::icons.close" height="h-3" width="w-3"/></button>
    </div>
    <button type="button" class="btn btn-sm bg-inherit border-none focus:outline-primary-focus text-base-content hover:bg-base-100 opacity-50" @click.prevent.stop="visible=true; $nextTick(() => $refs.search_box.focus());">
        <x-easyadmin::display.icon icon="easyadmin::icons.search" height="h-6" width="w-6"/>
    </button>
</div>
