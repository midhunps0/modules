@props(['itemsName', 'url', 'searchUrl', 'routeName', 'searchDisplayKeys'])
<form x-data="{
    id: 0,
    search: '',
    url: '{{$url}}',
    searchUrl: '{{$searchUrl}}',
    list: [],
    getItemsList() {
        axios.get(this.searchUrl, {params: {search: this.search}}).then((r) => {
            this.list = r.data.data.{{$itemsName}};
            console.log(this.list);
            console.log('ln: '+this.list.length);
        });
    }
}"
    @submit.prevent.stop="getItemsList"
    action="" class="flex flex-row items-end space-x-4">
    <div class="form-control w-52 relative">
        {{-- <label class="label">
          <span class="label-text">Code/Name</span>
        </label> --}}
        <input x-model="search"
            @input.deboune="if (search.length > 2) {getItemsList()}"
            @keyup="if($event.code=='Escape') {
                search='';
                list=[];
            }"
            type="text" placeholder="Code/Name" class="input input-bordered input-sm w-full max-w-xs" />
        <ul x-show="list.length > 0"
            @click.outside="search=''; list=[];"
            class="absolute top-10 left-0 z-10 flex flex-col bg-base-200 p-2 rounded-sm shadow-sm">
            <template x-for="item in list">
                <a :href="url.replace('0', id), route: '{{$routeName}}'" @click.prevent.stop="id = item.id;
                    $dispatch('linkaction', { link: url.replace('0', id), route: '{{$routeName}}'}); list=[];"
                    @keyup="if($event.code=='Escape') {
                        search='';
                        list=[];
                    }" class="btn-link text-left border-b border-base-300 py-1 text-base-content hover:text-warning hover:no-underline focus:text-warning">
                    @foreach ($searchDisplayKeys as $key)
                        <span x-text="item.{{$key}}"></span>@if(!$loop->last),@endif
                    @endforeach

                </a>
            </template>
        </ul>
    </div>
    <button class="btn btn-sm" type="submit">Search</button>
</form>