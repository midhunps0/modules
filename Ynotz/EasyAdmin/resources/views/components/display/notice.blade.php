<div x-data="{
        message: '',
        showmodal: false,
        mode: 'success',
        redirectRoute: null,
        redirectUrl: null,
        doClose() {
            this.showmodal=false;
            if (this.redirectRoute != null) {
                $dispatch('linkaction', {link: this.redirectUrl, route: this.redirectRoute, fresh: true});
            }
        }
    }" class="fixed top-0 left-0 z-50 w-screen h-screen bg-base-300 bg-opacity-40 flex flex-col justify-evenly"
    x-show="showmodal"
    @shownotice.window="message = $event.detail.message; mode = $event.detail.mode; redirectUrl = $event.detail.redirectUrl; redirectRoute = $event.detail.redirectRoute; showmodal = true;"
    >
    <div class="mx-auto w-4/5 sm:w-2/3 md:w-2/5 p-4 rounded-md shadow-md bg-base-100 border-base-300 text-center flex flex-col items-center space-y-4">
        <span x-text="message"></span>
        <button class="btn btn-sm w-32 px-4" :class="mode == 'success' ? 'btn-success' : 'btn-error'" @click="doClose();">Ok</button>
    </div>
    <div></div>
</div>
