@props(['title' => 'Menu Item', 'icon' => 'easyadmin::icons.info'])
<a x-data="{collapsed: false}"
    @sidebarresize.window="collapsed = $event.detail.collapsed;"
    @navresize.window="collapsed = $event.detail.navcollapsed;"
    class="block md:flex-row flex-wrap items-stretch my-0 text-sm px-2 hover:bg-base-300 h-fit">
    <x-easyadmin::display.icon icon="{{$icon}}" height="h-4" width="w-4"/>
    <span class="inline-block py-2 transition-all" :class="collapsed ? 'w-0 px-0' : 'w-40 px-2'" x-transition>
        <span class="block w-36 transition-opacity" :class="!collapsed || 'opacity-0'">{{$title}}</span>
    </span>
</a>
