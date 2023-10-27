<div x-data="{darktheme: $persist(true)}" class="relative w-5 flex flex-row items-center" >
    <button @click="darktheme=!darktheme; $dispatch('themechange', {darktheme: darktheme});" class="flex flex-row items-center justify-center h-5 w-5 p-0 m-0 focus:outline-primary">
        <x-easyadmin::display.icon  x-show="darktheme"
            icon="easyadmin::icons.moon" class="absolute top-o left-0" height="h-5" width="w-5"/>
        <x-easyadmin::display.icon  x-show="!darktheme"
            icon="easyadmin::icons.sun" class="absolute top-o left-0" height="h-5" width="w-5"/>
    </button>
</div>
