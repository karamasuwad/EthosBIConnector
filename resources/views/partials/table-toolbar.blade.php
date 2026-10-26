{{-- Search box + page-size selector. Expects $placeholder; reads ?search= and ?per_page= --}}
@php($perPage = (int) request('per_page', 10))
<form method="GET" action="{{ url()->current() }}" class="flex flex-wrap items-center justify-between gap-3 mb-4">
    <div class="flex items-center gap-2">
        <input type="search" name="search" value="{{ request('search') }}" placeholder="{{ $placeholder ?? 'Search...' }}"
               class="w-72 rounded-md border-gray-300 shadow-sm text-sm focus:border-teal-500 focus:ring-teal-500">
        <button type="submit" class="bg-teal-600 text-white px-4 py-2 rounded-md text-sm hover:bg-teal-700 transition duration-200">Search</button>
        @if(request()->filled('search'))
            <a href="{{ url()->current() }}?per_page={{ $perPage }}" class="text-sm text-gray-600 hover:text-gray-900">Clear</a>
        @endif
    </div>
    <label class="flex items-center gap-2 text-sm text-gray-600">
        Show
        <select name="per_page" onchange="this.form.submit()" class="rounded-md border-gray-300 shadow-sm text-sm focus:border-teal-500 focus:ring-teal-500">
            @foreach([10, 25, 50, 100] as $size)
                <option value="{{ $size }}" @selected($perPage === $size)>{{ $size }}</option>
            @endforeach
        </select>
        per page
    </label>
</form>
