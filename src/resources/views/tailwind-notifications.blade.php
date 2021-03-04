@if (count($errors) > 0)
<div x-data="{ show: true }" x-show="show"
    class="flex justify-between items-center bg-red-500 relative text-white py-3 px-3 mt-5 mb-5 rounded-lg">
    <div>
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    <div>
        <button type="button" @click="show = false" class="text-white hover:text-red-200">
            <span class="text-2xl">&times;</span>
        </button>
    </div>
</div>
@endif

@if(session()->has('message'))
<div x-data="{ show: true }" x-show="show"
    class="flex justify-between items-center bg-green-500 relative text-white py-3 px-3 mt-5 mb-5 rounded-lg">
    <div>
        {{ session('message') }}
    </div>
    <div>
        <button type="button" @click="show = false" class="text-white hover:text-green-200">
            <span class="text-2xl">&times;</span>
        </button>
    </div>
</div>
@endif
