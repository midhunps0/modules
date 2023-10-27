@props(['selectionEnabled' => true])
<div x-data="{ dropopen: false, url_all: '', url_selected: '', selectedCount: 0 }"
    @downloadurl.window="url_all = $event.detail.url_all; url_selected=$event.detail.url_selected; selectedCount = $event.detail.idscount;"
    @click.outside="dropopen = false;" class="relative">
    <label @click="dropopen = !dropopen;" tabindex="0" class="btn btn-outline hover:bg-transparent hover:text-base-content btn-sm m-1 normal-case">{{ __('Export') }}&nbsp;
        <x-easyadmin::display.icon icon="easyadmin::icons.down" />
    </label>
    <ul x-show="dropopen" tabindex="0"
        class="absolute top-5 right-0 z-50 p-2 shadow-md bg-base-200 rounded-md w-56 scale-90 origin-top-right transition-all duration-100 opacity-0"
        :class="!dropopen || 'top-8 scale-110 opacity-100'">
        @if($selectionEnabled)
        <li class="py-2 px-4" :class="selectedCount > 0 ? 'cursor-pointer hover:bg-base-100' : 'opacity-40'">
            <span x-show="selectedCount == 0">{{ __('Download Selected (.xlsx)') }}</span>
            <a x-show="selectedCount > 0" :href="url_selected" download>{{ __('Download Selected (.xlsx)') }}</a>
        </li>
        <li class="py-2 px-4" :class="selectedCount > 0 ? 'cursor-pointer hover:bg-base-100' : 'opacity-40'">
            <span x-show="selectedCount == 0">{{ __('Download Selected (.csv)') }}</span>
            <a x-show="selectedCount > 0" :href="url_selected + '&format=csv'" download>{{ __('Download Selected (.csv)') }}</a>
        </li>
        @endif
        <li class="py-2 px-4 hover:bg-base-100"><a :href="url_all" download>{{ __('Download All (.xlsx)') }}</a></li>
        <li class="py-2 px-4 hover:bg-base-100"><a :href="url_all + '&format=csv'" download>{{ __('Download All (.csv)') }}</a></li>
    </ul>
</div>
