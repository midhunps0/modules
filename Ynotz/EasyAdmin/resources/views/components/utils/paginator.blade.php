<div x-data="{
    currentPage: 0,
    totalItems: 0,
    lastPage: 0,
    itemsPerPage: 0,
    nextPageUrl: '',
    prevPageUrl: '',
    elements: [],
    firstItem: null,
    lastItem: null,
    count: 0,
    isFirstPage() {
        return this.currentPage == 1;
    },
    isLastPage() {
        return this.currentPage == this.lastPage;
    },
    hasMorePages() {
        return this.currentPage < this.lastPage;
    },
    getNextPage() {
        return this.currentPage < this.lastPage ? this.currentPage + 1 : this.currentPage;
    },
    getPrevPage() {
        return this.currentPage > 1 ? this.currentPage - 1 : this.currentPage;
    },
    getElements(els) {
        let elements = [];

        Object.keys(els).forEach((k) => {
            let el = els[k];
            if (typeof el === 'string' || el instanceof String) {
                elements.push({
                    page: el,
                    isLink: false,
                    link: null
                });
            } else {
                let keys = Object.keys(el);
                keys.forEach((k) => {
                    elements.push({
                        page: k,
                        isLink: true,
                        link: el[k]
                    });
                });
            }
        });
        return elements;
    },
    setPaginator(paginator) {
        this.currentPage = paginator.currentPage;
        this.totalItems = paginator.totalItems;
        this.lastPage = paginator.lastPage;
        this.itemsPerPage = paginator.itemsPerPage;
        this.nextPageUrl = paginator.nextPageUrl;
        this.prevPageUrl = paginator.prevPageUrl;
        this.elements = this.getElements(paginator.elements);
        this.firstItem = paginator.firstItem;
        this.lastItem = paginator.lastItem;
        this.count = paginator.count;

    }
}"
@setpagination.window="setPaginator($event.detail.paginator);">
    <nav x-show="totalItems / itemsPerPage > 1" role="navigation" aria-label="{{ __('Pagination Navigation') }}"
        class="flex items-center justify-between">
        <div class="flex justify-between flex-1 sm:hidden">
            <span x-show="isFirstPage()"
                class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-base-content bg-base-100 border border-base-100 cursor-default leading-5 rounded-md">
                {!! __('pagination.previous') !!}
            </span>

            <a x-show="!isFirstPage()" :href="prevPageUrl"
                @click.prevent.stop="$dispatch('paginator', {page: getPrevPage()});"
                class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-base-content bg-base-300 border border-base-100 leading-5 rounded-md hover:text-base-content focus:outline-none focus:ring ring-base-100 focus:border-blue-300 active:bg-base-300 active:text-base-content transition ease-in-out duration-150">
                {!! __('pagination.previous') !!}
            </a>


            <a x-show="hasMorePages()" :href="nextPageUrl"
                @click.prevent.stop="$dispatch('paginator', {link: getNextPage()});"
                class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-base-content bg-base-300 border border-base-100 leading-5 rounded-md hover:text-base-content focus:outline-none focus:ring ring-base-100 focus:border-blue-300 active:bg-base-300 active:text-base-content transition ease-in-out duration-150">
                {!! __('pagination.next') !!}
            </a>

            <span x-show="!hasMorePages()"
                class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-base-content bg-base-300 border border-base-100 cursor-default leading-5 rounded-md">
                {!! __('pagination.next') !!}
            </span>

        </div>

        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between flex-wrap">
            <div>
                <p class="text-sm text-base-content leading-5">
                    {!! __('Showing') !!}
                    <span x-show="firstItem != null" class="font-medium" x-text="firstItem"></span>
                    {!! __('to') !!}
                    <span x-show="firstItem != null" class="font-medium" x-text="lastItem"></span>

                    <span x-show="!firstItem" x-text="count"></span>

                    {!! __('of') !!}
                    <span class="font-medium" x-text="totalItems"></span>
                    {!! __('results') !!}
                </p>
            </div>

            <div>
                <span class="relative z-0 inline-flex shadow-sm rounded-md">
                    {{-- Previous Page Link --}}
                    <span x-show="isFirstPage()" aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
                        <span
                            class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-base-content bg-base-200 border border-base-100 cursor-default rounded-l-md leading-5"
                            aria-hidden="true">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                        </span>
                    </span>

                    <a x-show="!isFirstPage()" :href="prevPageUrl"
                        @click.prevent.stop="$dispatch('paginator', {link: getPrevPage()});" rel="prev"
                        class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-base-content bg-base-300 border border-base-100 rounded-l-md leading-5 hover:text-base-100 focus:z-10 focus:outline-none focus:ring ring-base-100 focus:border-blue-300 active:bg-base-300 active:text-base-content transition ease-in-out duration-150"
                        aria-label="{{ __('pagination.previous') }}">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                clip-rule="evenodd" />
                        </svg>
                    </a>


                    {{-- Pagination Elements --}}
                    <template x-for="(el, index) in elements" :key="index">

                        <span>
                            {{-- "Three Dots" Separator --}}
                            <span x-show="!el.isLink"
                                class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-base-content bg-base-300 border border-base-100 cursor-default leading-5"
                                aria-disabled="true">
                                <span x-text="el.page"></span>
                            </span>

                            {{-- Array Of Links" --}}
                            <span x-show="el.isLink && el.page == currentPage"
                                class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-base-content bg-base-200 border border-base-100 cursor-default leading-5"
                                aria-current="page" x-text="el.page">
                            </span>

                            <a x-show="el.isLink && el.page != currentPage" :href="el.link"
                                @click.prevent.stop="$dispatch('paginator', {page: el.page});"
                                class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-base-content bg-base-300 border border-base-100 leading-5 hover:text-base-content focus:z-10 focus:outline-none focus:ring ring-base-100 focus:border-blue-300 active:bg-base-300 active:text-base-content transition ease-in-out duration-150"
                                :aria-label="'Go to ' + el.page" x-text="el.page">
                            </a>
                        </span>
                    </template>

                    {{-- Next Page Link --}}

                    <a x-show="hasMorePages()" :href="nextPageUrl"
                        @click.prevent.stop="$dispatch('paginator', {page: getNextPage()});" rel="next"
                        class="relative inline-flex items-center px-2 py-2 -ml-px text-sm font-medium text-base-content bg-base-300 border border-base-100 rounded-r-md leading-5 hover:text-base-100 focus:z-10 focus:outline-none focus:ring ring-base-100 focus:border-blue-300 active:bg-base-300 active:text-base-content transition ease-in-out duration-150"
                        aria-label="{{ __('pagination.next') }}">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                clip-rule="evenodd" />
                        </svg>
                    </a>

                    <span x-show="!hasMorePages()" aria-disabled="true" aria-label="{{ __('pagination.next') }}">
                        <span
                            class="relative inline-flex items-center px-2 py-2 -ml-px text-sm font-medium text-base-content bg-base-300 border border-base-100 cursor-default rounded-r-md leading-5"
                            aria-hidden="true">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                        </span>
                    </span>

                </span>
            </div>
        </div>
    </nav>
</div>
