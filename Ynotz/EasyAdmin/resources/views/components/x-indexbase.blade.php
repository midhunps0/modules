@props(['x_ajax', 'title', 'indexUrl', 'selectIdsUrl', 'results', 'results_name', 'items_count', 'items_ids', 'selected_ids', 'total_results', 'current_page', 'unique_str', 'results_json' => '', 'result_calcs' => [], 'selectionEnabled', 'total_cols', 'adv_fields' => '', 'enableAdvSearch' => false, 'paginator', 'columns' => [], 'downloadUrl' => '', 'id' => '', 'row_counts' => config('easyadmin.table_row_counts'), 'show_settings' => 'true'])

@if (isset($partial) && $partial == 'index')
    {{$slot}}
@else
    <x-easyadmin::partials.adminpanel
        :x_ajax="$x_ajax"

        >
        {{-- title part --}}
        <div class="pb-4">
            <h3 class="text-xl font-bold pb-2"><span>{{ $title }}</span>&nbsp;</h3>
            <div class="flex flex-row flex-wrap justify-between items-center space-x-4">
                <div>
                    <a href="#" role="button" class="btn btn-xs">Add&nbsp;
                        <x-easyadmin::display.icon icon="icons.plus" />
                    </a>
                </div>
                <div class="flex flex-row flex-wrap justify-end items-center space-x-4">
                    <x-easyadmin::utils.panelresize />
                    <div>
                        <div x-data="{ dropopen: false, url_all: '', url_selected: '', selectedCount: 0 }"
                            @downloadurl.window="url_all = $event.detail.url_all; url_selected=$event.detail.url_selected; selectedCount = $event.detail.idscount;"
                            @click.outside="dropopen = false;" class="relative">
                            <label @click="dropopen = !dropopen;" tabindex="0"
                                class="btn btn-xs m-1">{{ __('Export') }}&nbsp;
                                <x-easyadmin::display.icon icon="icons.down" />
                            </label>
                            <ul x-show="dropopen" tabindex="0"
                                class="absolute top-5 right-0 z-50 p-2 shadow-md bg-base-200 rounded-md w-52 scale-90 origin-top-right transition-all duration-100 opacity-0"
                                :class="!dropopen || 'top-8 scale-110 opacity-100'">
                                <li class="py-2 px-4" :class="selectedCount > 0 ? 'cursor-pointer hover:bg-base-100' : 'opacity-40'">
                                    <span x-show="selectedCount == 0">{{ __('Download Selected') }}</span>
                                    <a x-show="selectedCount > 0" :href="url_selected"
                                        download>{{ __('Download Selected') }}</a>
                                    </li>
                                <li class="py-2 px-4 hover:bg-base-100"><a :href="url_all"
                                        download>{{ __('Download All') }}</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{$slot}}
    </x-easyadmin::partials.adminpanel>
@endif
