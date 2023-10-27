<div>
    <button @click.prevent.stop="showAdvSearch = true;"
        @keydown.window="
        if($event.altKey && $event.keyCode == 65) {
            showAdvSearch = true;
        }
        if($event.altKey && $event.shiftKey && $event.keyCode == 65) {
            showAdvSearch = false;
        }
        "
        class="btn btn-sm normal-case py-0 px-1 hover:bg-base-300 hover:text-warning transition-colors rounded-md flex flex-row items-center justify-center"
        :class="noconditions || 'bg-accent text-base-200'">
    Adv. Search&nbsp;<x-easyadmin::display.icon icon="easyadmin::icons.doc_search" />
    </button>
</div>
