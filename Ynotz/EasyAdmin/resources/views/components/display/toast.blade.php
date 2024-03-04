<div x-cloak x-data="{
        message: '',
        showmodal: false,
        mode: 'success',
        getDisplayTheme() {
            switch(this.mode) {
                case 'success':
                    return 'bg-success';
                    break;
                case 'error':
                    return 'bg-error';
                    break;
                default:
                    return 'bg-warinng';
                    break;
            }
        },
        init() {
            setTimeout(() => {
                this.showmodal = false;
            }, 2000);
        }
    }" class="fixed top-24 right-12 mx-auto w-4/5 sm:w-2/3 md:w-1/5 rounded-md shadow-md bg-base-100 border-base-300 text-center"
    x-show="showmodal"
    @showtoast.window="message = $event.detail.message; mode = $event.detail.mode; showmodal = true; init();"
    x-transition.duration-1000
    >
    <div class="h-full w-full text-center p-10 bg-opacity-70 rounded-md" :class="getDisplayTheme();">
        <span x-text="message"></span>
    </div>
    <div></div>
</div>
