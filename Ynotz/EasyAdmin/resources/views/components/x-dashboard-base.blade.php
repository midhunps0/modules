@if ($ajax)
    <div class="flex-grow bg-base-100 overflow-hidden shadow-sm sm:rounded-lg">
            {{$slot}}
    </div>
@else
<div>
    <x-easyadmin::app-layout>
        <div class="py-1 flex-grow flex flex-row h-full items-stretch w-full space-x-1">
            <div class="bg-base-100 overflow-hidden shadow-sm sm:rounded-md min-w-fit">
                <x-easyadmin::partials.sidebar />
            </div>
                <div x-data
                @contentupdate.window="
                if ($event.detail.target == 'renderedpanel') {
                    $el.innerHTML = $event.detail.content;
                }
                "
                class="flex-grow bg-base-100 overflow-x-hidden shadow-sm sm:rounded-lg" id="renderedpanel">
                    {{$slot}}
                </div>
            <div class="h-full w-full absolute top-0 left-0 z-50 bg-base-200 opacity-30" x-show="ajaxLoading"></div>
        </div>
    </x-easyadmin::app-layout>
</div>
@endif
