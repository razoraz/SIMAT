@if ($paginator->hasPages())
    <style>
        .simat-pagination-nav {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            gap: 0.75rem;
            width: 100%;
            min-width: 0;
        }
        .simat-pagination-info {
            width: 100%;
            text-align: center;
        }
        .simat-pagination-list {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: center;
            gap: 0.375rem;
            width: 100%;
            max-width: 100%;
        }
        .simat-page-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 34px;
            height: 34px;
            padding: 0 0.625rem;
            border-radius: 0.75rem;
            font-size: 0.75rem;
            line-height: 1;
            font-weight: 700;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            background-color: #020617;
            color: #cbd5e1;
            border: 1px solid #1e293b;
            transition: all 0.15s ease;
            text-decoration: none;
            user-select: none;
        }
        .simat-page-btn:hover {
            background-color: #1e293b;
            color: #6ee7b7;
            border-color: rgba(16, 185, 129, 0.45);
        }
        .simat-page-btn-arrow {
            padding: 0 0.5rem;
        }
        .simat-page-btn-active {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 34px;
            height: 34px;
            padding: 0 0.625rem;
            border-radius: 0.75rem;
            font-size: 0.75rem;
            line-height: 1;
            font-weight: 800;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            background-color: #10b981;
            color: #020617;
            border: 1px solid #34d399;
            box-shadow: 0 8px 18px -4px rgba(16, 185, 129, 0.28);
            user-select: none;
        }
        .simat-page-btn-disabled {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 34px;
            height: 34px;
            padding: 0 0.5rem;
            border-radius: 0.75rem;
            background-color: rgba(15, 23, 42, 0.8);
            color: #475569;
            border: 1px solid rgba(30, 41, 59, 0.8);
            cursor: not-allowed;
            user-select: none;
        }
        .simat-page-dots {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 26px;
            height: 34px;
            padding: 0 0.25rem;
            font-size: 0.75rem;
            font-weight: 700;
            color: #64748b;
            user-select: none;
        }
        @media (min-width: 1024px) {
            .simat-pagination-nav {
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
                text-align: left;
                gap: 1rem;
            }
            .simat-pagination-info {
                width: auto;
                text-align: left;
                flex-shrink: 0;
            }
            .simat-pagination-list {
                width: auto;
                justify-content: flex-end;
            }
        }
    </style>

    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="simat-pagination-nav">
        {{-- Ringkasan Jumlah Data --}}
        <div class="simat-pagination-info text-xs text-slate-400">
            Menampilkan
            @if ($paginator->firstItem())
                <span class="font-bold text-white font-mono">{{ number_format($paginator->firstItem()) }}</span>
                &ndash;
                <span class="font-bold text-white font-mono">{{ number_format($paginator->lastItem()) }}</span>
            @else
                <span class="font-bold text-white font-mono">{{ number_format($paginator->count()) }}</span>
            @endif
            dari
            <span class="font-bold text-emerald-400 font-mono">{{ number_format($paginator->total()) }}</span>
            data
        </div>

        {{-- Daftar Tombol Halaman (Fleksibel Wrap & Aman Anti-Overflow) --}}
        <div class="simat-pagination-list">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}" class="simat-page-btn-disabled">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="{{ __('pagination.previous') }}"
                    class="simat-page-btn simat-page-btn-arrow active:scale-95">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                </a>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span aria-disabled="true" class="simat-page-dots">
                        {{ $element }}
                    </span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page" class="simat-page-btn-active">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}" aria-label="{{ __('Go to page :page', ['page' => $page]) }}"
                                class="simat-page-btn active:scale-95">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="{{ __('pagination.next') }}"
                    class="simat-page-btn simat-page-btn-arrow active:scale-95">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                </a>
            @else
                <span aria-disabled="true" aria-label="{{ __('pagination.next') }}" class="simat-page-btn-disabled">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                </span>
            @endif
        </div>
    </nav>
@endif
