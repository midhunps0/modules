<x-easyadmin::app-layout>
    <div class="py-1 flex-grow flex flex-row h-full items-stretch w-full space-x-1">
        <div class="bg-base-100 overflow-hidden shadow-sm sm:rounded-md min-w-fit relative">
            <x-easyadmin::partials.sidebar />
        </div>
        <div x-data
            @contentupdate.window="
            console.log('target: '+ $event.detail.target);
            console.log('id: '+ $el.id);
                    if ($event.detail.target == $el.id) {
                        $el.innerHTML = $event.detail.content;
                    }
                "
            class="flex-grow bg-base-100 overflow-x-hidden shadow-sm sm:rounded-lg" id="renderedpanel">

            @fragment('main-panel')
                <div x-data="{
                    showAdvSearch: false,
                    noconditions: true,
                }" class="p-3 border-b border-base-200 overflow-x-scroll relative h-full"
                    :id="$id('panel-base')">
                    {{ $slot }}
                </div>
            @endfragment

        </div>
        <div x-show="ajaxLoading" class="h-full w-full absolute top-0 left-0 z-50 bg-base-200 opacity-30 flex flex-row justify-center items-center">
            <img src="/images/loading.gif" class="h-10 w-10" alt="" />
        </div>
    </div>
</x-easyadmin::app-layout>
