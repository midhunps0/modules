@props(['message', 'okFunction', 'cancelFunction', 'showKey' => 'showmodal', 'action_label' => 'Ok'])
<div x-data="{
        message: '',
    }" class="fixed top-0 left-0 z-50 w-screen h-screen bg-base-300 bg-opacity-40 flex flex-col justify-evenly"
    x-show="{{$showKey}}"
    x-init="message='{{$message}}'"
    >
    <div class="mx-auto w-4/5 sm:w-2/3 md:w-2/5 p-4 rounded-md shadow-md bg-base-100 border-base-300 text-center flex flex-col items-center space-y-4">
        <span x-text="message"></span>
        <div class="flex flex-row space-x-8 p-4">
            <button type="button" class="btn btn-sm w-32 px-4" @click.prevent.stop="{{$showKey}} = false; {{$cancelFunction}}();">Cancel</button>
            <button type="button" class="btn btn-sm w-32 px-4 btn-error" @click.prevent.stop="{{$showKey}} = false; {{$okFunction}}(); ">{{$action_label}}</button>
        </div>
    </div>
    <div></div>
</div>
