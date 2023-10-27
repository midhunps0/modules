<button x-data="{navcollapsed: $persist(false)}"
        x-init="
            $dispatch('navresize', {navcollapsed: navcollapsed});
        " class="hidden md:inline-block btn btn-sm btn-neutral fixed bottom-3 right-3 z-50" :class="!navcollapsed || 'text-warning'" @click.prevent.stop="navcollapsed = !navcollapsed; $dispatch('navresize', {navcollapsed: navcollapsed});">
    <x-easyadmin::display.icon x-show="navcollapsed" icon="easyadmin::icons.minus_circle" height="h-4" width="w-4"/>
    <x-easyadmin::display.icon x-show="!navcollapsed" icon="easyadmin::icons.expand" height="h-4" width="w-4"/>
</button>
