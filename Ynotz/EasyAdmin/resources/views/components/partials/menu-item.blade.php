@props(['title' => 'Menu Item', 'route' => '', 'href' => '#', 'icon' => 'easyadmin::icons.info'])
{{-- <a x-data="{collapsed: false}"
    @sidebarresize.window="collapsed = $event.detail.collapsed;"
    @click.prevent.stop="$dispatch('linkaction', {link: '{{$href}}', route: '{{$route}}'}); $dispatch('sidebarvisibility', {hidden: true});"
    href="{{$href}}" class="flex flex-row md:hidden items-center my-0 text-sm px-3 hover:bg-base-300"
    :class="currentroute != '{{$route}}' || 'text-accent font-bold bg-base-300'">
    <x-easyadmin::display.icon icon="{{$icon}}" height="h-4" width="w-4"/>
    <span class="inline-block py-2 transition-all" :class="collapsed ? 'w-0 px-0' : 'w-40 px-3'" x-transition>
        <span class="block w-36 transition-opacity" :class="!collapsed || 'opacity-0'">{{$title}}</span>
    </span>
</a> --}}
<a x-data="{collapsed: false}"
    @sidebarresize.window="collapsed = $event.detail.collapsed; console.log(`collapsed: ${collapsed}`)"
    @navresize.window="collapsed = $event.detail.navcollapsed;"
    @click.prevent.stop="
        $dispatch('linkaction', {link: '{{$href}}', route: '{{$route}}'});
        if(window.innerWidth < 768) {hidden = true;}
    "
    href="{{$href}}" class="flex md:flex-row flex-wrap items-center my-0 text-sm px-2 hover:bg-base-300 h-fit"
    :class="currentroute != '{{$route}}' || 'text-primary font-bold'">
    <x-easyadmin::display.icon icon="{{$icon}}" height="h-4" width="w-4"/>
    <span class="block py-2 transition-all overflow-x-hidden" :class="collapsed ? 'hiddden w-0 px-0' : 'w-40 px-2'" x-transition>{{$title}}
    </span>
</a>
