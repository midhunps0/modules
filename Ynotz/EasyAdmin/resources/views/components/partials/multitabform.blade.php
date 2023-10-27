@props([
    'form',
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
        'full' => 'w-full'
    ];
    $form_width = isset($form['width']) && isset($wclasses[$form['width']]) ? $wclasses[$form['width']] : 'w-1/2';
@endphp
<div class="flex flex-col space-y-2 w-full items-center">
<div class="w-full text-right px-4">
    <a href="#" class="btn btn-sm py-2 normal-case" @click.prevent.stop="window.history.back();">Go Back</a>
</div>
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
class="{{$form_width}} border border-base-300 bg-base-200 shadow-sm rounded-md p-3 flex flex-col"
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
>
    @fragment('create_form')
        {{-- <h3 class="text-xl text-center">{{$form['title']}}</h3> --}}
        <div class="flex flex-col space-y-8">
            @if ($form['method'] != 'POST')
                @method($form['method'])
            @endif

            @if ($form['layout'] != null && count($form['layout']) > 0 && $form['layout']['item_type'] == 'layout')
                <x-easyadmin::partials.layoutrenderer
                    :item="$form['layout']" :form_items="$form['items']"/>
            @elseif (isset($form['layout']) == null)
                @foreach ($form['items'] as $item)
                    <x-easyadmin::partials.inputrenderer :item="$item" />
                @endforeach
            @endif
            <div class="flex flex-row justify-between mt-8 mb-4">
                <a href="#" class="btn btn-link py-2 normal-case" @click.prevent.stop="cancelRoute != null ? $dispatch('linkaction', {link: cancelRoute}) : window.history.back()">{{$form['cancel_label']}}</a>
                <button class="btn btn-sm py-2" type="submit">{{$form['submit_label']}}</button>
            </div>
        </div>
    @endfragment
    {{-- <div class="form-control flex flex-row w-full mb-4">
        <a href="#" class="btn btn-link py-2 normal-case" @click.prevent.stop="window.history.back();">{{$form['cancel_label']}}</a>
    </div> --}}
</form>
</div>
