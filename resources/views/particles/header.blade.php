<style>
    .list-unstyled {
        max-height: 300px;
        overflow-y: scroll;
        -ms-overflow-style: none;  /* Internet Explorer 10+ */
        scrollbar-width: none;  /* Firefox */
    }
</style>

<div class="header">
    <div class="header-content">
        <nav class="navbar navbar-expand">
            <div class="collapse navbar-collapse justify-content-between">
                <div class="header-left">

                </div>

                <ul class="navbar-nav header-right">
                    <li class="nav-item dropdown notification_dropdown">
                        <a class="nav-link" href="#" role="button" data-toggle="dropdown">
                            <i class="mdi mdi-bell"></i>
                            {{-- <div class="pulse-css"></div> --}}
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            @if(isset($notifications) && $notifications->count())
                            <ul class="list-unstyled"  style="max-height: 300px; overflow-y: scroll;">
                                {{-- <li class="media dropdown-item">
                                    <span class="success"><i class="ti-user"></i></span>
                                    <div class="media-body">
                                        <a href="#">
                                            <p><strong>Martin</strong> has added a <strong>customer</strong> Successfully
                                            </p>
                                        </a>
                                    </div>
                                    <span class="notify-time">3:20 am</span>
                                </li>
                                <li class="media dropdown-item">
                                    <span class="primary"><i class="ti-shopping-cart"></i></span>
                                    <div class="media-body">
                                        <a href="#">
                                            <p><strong>Jennifer</strong> purchased Light Dashboard 2.0.</p>
                                        </a>
                                    </div>
                                    <span class="notify-time">3:20 am</span>
                                </li>
                                <li class="media dropdown-item">
                                    <span class="danger"><i class="ti-bookmark"></i></span>
                                    <div class="media-body">
                                        <a href="#">
                                            <p><strong>Robin</strong> marked a <strong>ticket</strong> as unsolved.
                                            </p>
                                        </a>
                                    </div>
                                    <span class="notify-time">3:20 am</span>
                                </li>
                                <li class="media dropdown-item">
                                    <span class="primary"><i class="ti-heart"></i></span>
                                    <div class="media-body">
                                        <a href="#">
                                            <p><strong>David</strong> purchased Light Dashboard 1.0.</p>
                                        </a>
                                    </div>
                                    <span class="notify-time">3:20 am</span>
                                </li> --}}
                                @foreach ($notifications as $notification)
                                    <li class="media dropdown-item">
                                        <span class="success"><i class="ti-image"></i></span>
                                        <div class="media-body">
                                            <a href="#">
                                                <p class="notification-message"><strong>
                                                    {{ $notification->data['user_name'] ?? 'Unknown' }}</strong> <strong>{{ $notification->data['message'] }} </strong>
                                                </p>
                                            </a>
                                        </div>
                                        <span class="notify-time">{{ $notification->created_at->diffForHumans() }}</span>
                                    </li>
                                @endforeach
                            </ul>  
                            <a class="all-notification" href="#">See all notifications <i
                                class="ti-arrow-right"></i></a>   
                                @else
                                <li class="media dropdown-item">No notifications</li>
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
                                <span class="ml-2">Profile </span>
                            </a>
                            <a href="{{ routePut('app.logout') }}" class="dropdown-item">
                                <i class="icon-key"></i>
                                <span class="ml-2">Logout </span>
                            </a>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</div>

<script>
    function markAsRead(notificationId) {
        // Make an AJAX request to mark the notification as read
        $.ajax({
            url: '/mark-notification-as-read/' + notificationId,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                // Optionally, update UI or perform other actions upon successful marking as read
            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });
    }
</script>
