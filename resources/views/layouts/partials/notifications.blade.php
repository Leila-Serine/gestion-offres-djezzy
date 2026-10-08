{{-- La même cloche est utilisée dans la topbar ordinateur et mobile. --}}
@php $nombreNotifications = $notificationsNonLues->count(); @endphp

<div class="dropdown">
    <button type="button" class="notification-trigger" data-bs-toggle="dropdown" aria-expanded="false" aria-controls="{{ $dropdownId }}" aria-label="Notifications : {{ $nombreNotifications }} non lue{{ $nombreNotifications > 1 ? 's' : '' }}">
        <i class="bi bi-bell" aria-hidden="true"></i>
        @if ($nombreNotifications > 0)
            <span class="notification-count" aria-hidden="true">{{ $nombreNotifications > 9 ? '9+' : $nombreNotifications }}</span>
        @endif
    </button>

    <div class="dropdown-menu dropdown-menu-end notification-menu" id="{{ $dropdownId }}">
        <div class="notification-menu-header">
            <div><strong>Notifications</strong><p>{{ $nombreNotifications > 0 ? $nombreNotifications . ' non lue' . ($nombreNotifications > 1 ? 's' : '') : 'Aucune nouvelle notification' }}</p></div>
            <i class="bi bi-bell" aria-hidden="true"></i>
        </div>

        @if ($nombreNotifications > 0)
            <div class="notification-menu-actions">
                <form method="POST" action="{{ route('notifications.read-all') }}">
                    @csrf
                    <button type="submit" class="btn btn-link btn-sm p-0 text-decoration-none">Tout marquer comme lu</button>
                </form>
            </div>
        @endif

        <div class="notification-list">
            @forelse ($notificationsNonLues->take(5) as $notification)
                {{-- Le contrôleur lié à cette route marque la notification puis redirige. --}}
                <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                    @csrf
                    <button type="submit" class="notification-item">
                        <span class="notification-item-icon"><i class="bi bi-chat-left-text" aria-hidden="true"></i></span>
                        <span class="notification-item-content">
                            <strong>{{ $notification->data['titre'] ?? 'Notification' }}</strong>
                            <span>{{ $notification->data['message'] ?? '' }}</span>
                            <small>{{ $notification->created_at?->diffForHumans() }}</small>
                        </span>
                    </button>
                </form>
            @empty
                <div class="notification-empty"><span class="empty-icon"><i class="bi bi-bell-slash" aria-hidden="true"></i></span><strong>Tout est à jour</strong><p>Vous n'avez aucune notification non lue.</p></div>
            @endforelse
        </div>
    </div>
</div>
