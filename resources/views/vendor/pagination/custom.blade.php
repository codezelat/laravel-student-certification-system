@if ($paginator->hasPages())
    <div class="pagination" style="display: flex; justify-content: space-between; align-items: center;">
        <div class="pagination-info" style="color: var(--text-secondary); font-size: 0.9rem;">
            Showing 
            <strong>{{ $paginator->firstItem() }}</strong>
            to
            <strong>{{ $paginator->lastItem() }}</strong>
            of
            <strong>{{ $paginator->total() }}</strong>
            results
        </div>

        <div class="pagination-links" style="display: flex; gap: 0.5rem;">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <button class="btn btn-secondary btn-sm" disabled style="opacity: 0.5; cursor: not-allowed;">
                    &laquo; Previous
                </button>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="btn btn-secondary btn-sm" rel="prev">
                    &laquo; Previous
                </a>
            @endif

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="btn btn-secondary btn-sm" rel="next">
                    Next &raquo;
                </a>
            @else
                <button class="btn btn-secondary btn-sm" disabled style="opacity: 0.5; cursor: not-allowed;">
                    Next &raquo;
                </button>
            @endif
        </div>
    </div>
@endif
