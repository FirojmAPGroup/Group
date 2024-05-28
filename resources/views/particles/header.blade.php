<style>
    .list-unstyled {
        max-height: 300px;
        overflow-y: scroll;
        -ms-overflow-style: none;  /* Internet Explorer 10+ */
        scrollbar-width: none;  /* Firefox */
    }
    .pulse-css {
        width: 1rem;
        height: 1rem;
        border-radius: 0.5rem;
        border-radius: 3.5rem;
        height: .4rem;
        position: absolute;
        background: #593bdb;
        right: 5px;
        top: .6rem;
        width: .4rem;
    }
</style>

<div class="header">
    <div class="header-content">
        <nav class="navbar navbar-expand">
            <div class="collapse navbar-collapse justify-content-between">
                <div class="header-left"></div>

                <ul class="navbar-nav header-right">
                    <li class="nav-item dropdown notification_dropdown">
                        <a class="nav-link" href="#" role="button" data-toggle="dropdown">
                            <i class="mdi mdi-bell"></i>
                            @if(isset($unreadCount) && $unreadCount > 0)
                                <div class="pulse-css"></div>
                            @endif
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            @if(isset($notifications) && $notifications->count() > 0)
                                <ul class="list-unstyled">
                                    @foreach ($notifications->take(5) as $notification)
                                        <li class="media dropdown-item">
                                            <span class="success"><i class="ti-image"></i></span>
                                            <div class="media-body">
                                                <a href="#" onclick="markAsRead('{{ $notification->id }}')">
                                                    <p class="notification-message">
                                                        <strong>{{ $notification->data['user_name'] ?? 'Unknown' }}</strong>
                                                        <strong>{{ $notification->data['message'] }}</strong>
                                                    </p>
                                                </a>
                                            </div>
                                            <span class="notify-time">{{ $notification->created_at->diffForHumans() }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                                <a class="all-notification" href="{{ route('all-notifications') }}">See all notifications <i class="ti-arrow-right"></i></a>
                            @else
                                <ul class="list-unstyled">
                                    <li class="media dropdown-item">No notifications</li>
                                </ul>
                            @endif
                        </div>
                    </li>
                    
                    <li class="nav-item dropdown header-profile">
                        <a class="nav-link" href="#" role="button" data-toggle="dropdown">
                            <i class="mdi mdi-account"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a href="{{ route('profile.view') }}" class="dropdown-item">
                                <i class="icon-user"></i>
                                <span class="ml-2">Profile</span>
                            </a>
                            <a href="{{ route('app.logout') }}" class="dropdown-item">
                                <i class="icon-key"></i>
                                <span class="ml-2">Logout</span>
                            </a>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const notificationDropdown = document.querySelector('.notification_dropdown');
        notificationDropdown.addEventListener('click', function () {
            const pulseElement = document.querySelector('.pulse-css');
            if (pulseElement) {
                pulseElement.style.display = 'none';
            }
        });
    });

    function markAsRead(notificationId) {
        // Make an AJAX request to mark the notification as read
        $.ajax({
            url: '/mark-notification-as-read/' + notificationId,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                // Optionally, update the UI to reflect the notification was read
            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });
    }
</script>
