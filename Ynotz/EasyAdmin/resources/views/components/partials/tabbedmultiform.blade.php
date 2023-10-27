@props([
    'form',
    'tabs',
    'errors',
    '_old'
])
@php
    $wclasses = [
        '1/2' => 'w-1/2',
        '1/3' => 'w-1/3',
        '1/4' => 'w-1/4',
        '2/3' => 'w-2/3',
        '2/5' => 'w-2/5',
        '3/4' => 'w-3/4',
        '3/5' => 'w-3/5',
        '4/5' => 'w-4/5',
        'full' => 'w-full'
    ];
    $form_width = isset($form['width']) && isset($wclasses[$form['width']]) ? $wclasses[$form['width']] : 'w-1/2';
    $tabWidthClasses = [
        '1' => 'w-full',
        '2' => 'w-1/2',
        '2' => 'w-1/2',
        '3' => 'w-1/3',
        '4' => 'w-1/4',
        '5' => 'w-1/5',
        '6' => 'w-1/6',
    ];
    $tabWidth = $tabWidthClasses[count($tabs)] ?? 'w-1/5';
@endphp
<div class="flex flex-col space-y-2 w-full items-center">
<div class="w-full text-right px-4">
    <a href="#" class="btn btn-sm py-2 normal-case" @click.prevent.stop="window.history.back();">Go Back</a>
</div>
{{-- TABS WRAPPER --}}
<div x-data="{
        currentTab: 0,
    }" class="w-full">
    <div class="tabs w-full overflow-y-auto">
        @foreach ($tabs as $form)
        <a class="tab {{$tabWidth}} tab-lifted" :class="currentTab != {{$loop->index}} || 'tab-active !bg-base-200'">{{$form['title']}}</a>
        @endforeach
        {{-- <a class="tab tab-lifted tab-active">Tab 2</a> --}}
    </div>
    <div class="w-full">
        @foreach ($tabs as $form)
        <form x-data="{
                postUrl: '',
                successRedirectUrl: null,
                successRedirectRoute: null,
                cancelRoute: null,
                doSubmit() {
                    let form = document.getElementById('{{$form['id']}}');
                    let fd = new FormData(form);
                    {{-- fd.append('x_fr', 'form_user_create'); --}}
                    $dispatch('formsubmit', { url: this.postUrl, formData: fd, target: '{{$form['id']}}', fragment: 'create_form' });
                }
            }" action="" id="{{$form['id']}}"
            @submit.prevent.stop="doSubmit();"
                @formresponse.window="console.log($event.detail);
                if ($event.detail.target == $el.id) {
                    if ($event.detail.content.success) {
                        $dispatch('shownotice', {message: $event.detail.content.message, mode: 'success', redirectUrl: successRedirectUrl, redirectRoute: successRedirectRoute});
                        $dispatch('formerrors', {errors: []});
                    } else if (typeof $event.detail.content.errors != undefined) {
                        $dispatch('formerrors', {errors: $event.detail.content.errors});
                    } else{
                        $dispatch('shownotice', {message: $event.detail.content.error, mode: 'error', redirectUrl: null, redirectRoute: null});
                    }
                }
            "
            class="border-r border-b border-l border-base-300 bg-base-200 shadow-sm rounded-b-md p-3 flex flex-col"
            x-init="
                postUrl = '{{ count($form['action_route_params']) > 0 ? route($form['action_route'], $form['action_route_params']) : route($form['action_route']) }}';
                @if (isset($form['success_redirect_route']))
                    successRedirectUrl = '{{route($form['success_redirect_route'])}}';
                    successRedirectRoute = '{{route($form['success_redirect_route'])}}';
                @endif
                @if (isset($form['cancel_route']))
                    cancelRoute = '{{route($form['cancel_route'])}}';
                @endif
            "
            x-show="currentTab == {{$loop->index}}"
            >
            @fragment('create_form')
                {{-- <h3 class="text-xl text-center my-2">{{$form['title']}}</h3> --}}
                {{-- <div class="divider mb-6"></div> --}}
                <div class="flex flex-col space-y-8 p-3 border border-base-100 rounded-md">
                    @foreach ($form['items'] as $item)
                        @if ($form['method'] != 'POST')
                            @method($form['method'])
                        @endif
                        @switch($item['item_type'])
                            @case('input')
                                @php
                                    $html_inputs = [
                                        "button",
                                        "checkbox",
                                        "color",
                                        "date",
                                        "datetime-local",
                                        "email",
                                        // "file",
                                        "hidden",
                                        // "image",
                                        "month",
                                        "number",
                                        "password",
                                        // "radio",
                                        // "range",
                                        // "reset",
                                        "search",
                                        // "submit",
                                        "tel",
                                        "text",
                                        "time",
                                        "url",
                                        "week",
                                    ];
                                @endphp
                                @if (in_array($item['input_type'], $html_inputs))
                                    <x-dynamic-component
                                    :component="'easyadmin::inputs.text'"
                                    :element="$item"
                                    :label_position="$form['label_position'] ?? 'top'"
                                    :_old="$_old ?? []"
                                    :xerrors="$errors ?? []"
                                    />
                                @else
                                    <x-dynamic-component :component="$item['input_type']"
                                    :element="$item"
                                    :label_position="$form['label_position'] ?? 'top'"
                                    :_old="$_old ?? []"
                                    :xerrors="$errors ?? []"
                                    />
                                @endif
                            @default
                                @break
                        @endswitch
                    @endforeach
                    <div class="flex flex-row justify-between mt-8 mb-4">
                        <a href="#" class="btn btn-link py-2 normal-case" @click.prevent.stop="cancelRoute != null ? $dispatch('linkaction', {link: cancelRoute}) : window.history.back()">{{$form['cancel_label']}}</a>
                        <button class="btn btn-sm py-2" type="submit">{{$form['submit_label']}}</button>
                    </div>
                </div>
            @endfragment
        </form>
        @endforeach
    </div>
</div>
{{-- END TABS WRAPPER --}}

</div>
