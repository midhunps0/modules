@props(['items_count', 'counts' => [10, 20, 30, 50, 100]])
<div x-data="{count: {{$items_count}} }" class="flex flex-row items-center w-48 space-x-2">
    <label for="items_count">Items per page: </label>
    <select x-model="count"
        @change="$dispatch('countchange', {count: count});"
        class="select select-bordered select-sm w-20 py-0">
        @foreach ($counts as $num)
          <option value="{{$num}}" @if($items_count == $num) selected @endif>{{$num}}</option>
        @endforeach
      </select>
</div>