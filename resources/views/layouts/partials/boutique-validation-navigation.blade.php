@if (
    auth()->check()
    && auth()->user()->role === 'commercial'
    && Route::has('commercial.boutiques.validation.index')
)
    <a
        href="{{ route('commercial.boutiques.validation.index') }}"
        class="sidebar-link {{ request()->routeIs('commercial.boutiques.validation.*') ? 'active' : '' }}"
        @if(request()->routeIs('commercial.boutiques.validation.*'))
            aria-current="page"
        @endif
    >
        <i class="bi bi-shop-window" aria-hidden="true"></i>
        <span>Validation boutiques</span>
    </a>
@endif
