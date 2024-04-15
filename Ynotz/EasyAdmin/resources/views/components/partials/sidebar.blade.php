<div x-data="{hidden: false}"
    x-init="
        setTimeout(() => {hidden = window.innerWidth < 768;}, 100);
    "
    @sidebarvisibility.window="hidden=$event.detail.hidden;"
    @resize.window="hidden = window.innerWidth < 768; if(hidden){
        $dispatch('sidebarresize', {'collapsed': false});
    }"
    class="overflow-x-hidden fixed top-0 left-0 z-50 md:relative bg-base-100 md:w-auto min-w-fit ransition-all h-full max-w-full"
    :class="hidden ? 'hidden' : 'md:block w-full'">
    <div x-show="!hidden" class="md:hidden w-full text-right pt-2 fixed top-2 right-2 z-20 mb-20">
        <button x-show="!hidden" @click.prevent.stop="hidden=true;" class="btn btn-md text-warning"><x-easyadmin::display.icon icon="easyadmin::icons.close"/></button>
    </div>
    <ul x-show="!hidden" x-transition class="mt-20 md:mt-0">
        @foreach ($sidebar_data as $item)
            @if ($item['type'] == 'menu_group')
                @if (!isset($item['show']) || (isset($item['show']) && $item['show']))
                <li x-data="{group_expand: true}">
                    <label class="flex flex-row items-center justify-start font-bold hover:bg-base-300">
                        <x-easyadmin::partials.menu-group title="{{$item['title']}}" icon="{{$item['icon']}}"/>
                        <span class="transition-all" :class="group_expand ? 'rotate-180' : 'rotate-0'" @click.prevent.stop="group_expand = !group_expand;">
                            <x-easyadmin::display.icon icon="easyadmin::icons.down" height="h-4" width="w-4" class="mx-1 z-20"/>
                        </span>
                    </label>
                    <ul x-data="{nof_items: {{count($item['menu_items'])}}, ht: $el.offsetHeight}" x-init="ht = $el.offsetHeight; setTimeout(() => {group_expand = false;}, 10);" class="overflow-hidden bg-base-200 bg-opacity-60 box-content transition-all"
                    :style="group_expand ? 'height: ' + ht + 'px;' : 'height: 0px;'"
                    >
                        @foreach ($item['menu_items'] as $mi)
                            @if ($mi['type'] == 'menu_item' && (!isset($mi['show']) || (isset($mi['show']) && $mi['show'])))
                            <li class="block overflow-visible"><x-easyadmin::partials.menu-item title="{{$mi['title']}}" route="{{$mi['route']}}" href="{{route($mi['route'], $mi['route_params'])}}" icon="{{$mi['icon']}}"/></li>
                            @endif
                        @endforeach
                    </ul>
                </li>
                @endif
            @elseif ($item['type'] == 'menu_item')
                @if (!isset($item['show']) || (isset($item['show']) && $item['show']))
                <li><x-easyadmin::partials.menu-item title="{{$item['title']}}" route="{{$item['route']}}" href="{{route($item['route'], $item['route_params'])}}" icon="{{$item['icon']}}"/></li>
                @endif
            @elseif ($item['type'] == 'menu_section')
            @if (!isset($item['show']) || (isset($item['show']) && $item['show']))
            <li class="flex flex-row items-center justify-start bg-base-200 bg-opacity-50 opacity-80 mt-4">
                <x-easyadmin::partials.menu-group title="{{$item['title']}}" icon="{{$item['icon']}}"/><span>:</span>
            </li>
            @endif
            @endif
        @endforeach
    </ul>
</div>
