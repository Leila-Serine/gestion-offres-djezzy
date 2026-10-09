@if (
    auth()->check()
    && auth()->user()->role === 'commercial'
    && Route::has('commercial.clients.validation.index')
)
    <a
        href="{{ route(
            'commercial.clients.validation.index'
        ) }}"
        class="sidebar-link
            {{ request()->routeIs(
                'commercial.clients.validation.*'
            ) ? 'active' : '' }}"
        @if (
            request()->routeIs(
                'commercial.clients.validation.*'
            )
        )
            aria-current="page"
        @endif
    >
        <i
            class="bi bi-person-check"
            aria-hidden="true"
        ></i>

        <span>
            Validation clients
        </span>
    </a>
@endif
