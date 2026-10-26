{{-- Showing x to y of z + page links. Expects a paginator in $paginator --}}
<div class="bg-white px-6 py-4 border-t border-gray-200">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="text-sm text-gray-700">
            Showing
            <span class="font-medium">{{ $paginator->firstItem() ?? 0 }}</span>
            to
            <span class="font-medium">{{ $paginator->lastItem() ?? 0 }}</span>
            of
            <span class="font-medium">{{ $paginator->total() }}</span>
            results
        </div>
        @if($paginator->hasPages())
            <div class="flex flex-wrap gap-2">
                @if ($paginator->onFirstPage())
                    <span class="px-3 py-1 text-gray-400 bg-gray-100 rounded-md cursor-not-allowed">Previous</span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" class="px-3 py-1 text-teal-600 bg-white border border-gray-300 rounded-md hover:bg-gray-50">Previous</a>
                @endif

                @foreach ($paginator->getUrlRange(max(1, $paginator->currentPage() - 2), min($paginator->lastPage(), $paginator->currentPage() + 2)) as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="px-3 py-1 text-white bg-teal-600 border border-teal-600 rounded-md">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="px-3 py-1 text-teal-600 bg-white border border-gray-300 rounded-md hover:bg-gray-50">{{ $page }}</a>
                    @endif
                @endforeach

                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" class="px-3 py-1 text-teal-600 bg-white border border-gray-300 rounded-md hover:bg-gray-50">Next</a>
                @else
                    <span class="px-3 py-1 text-gray-400 bg-gray-100 rounded-md cursor-not-allowed">Next</span>
                @endif
            </div>
        @endif
    </div>
</div>
