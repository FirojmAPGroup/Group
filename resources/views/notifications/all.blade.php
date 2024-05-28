@extends('layouts.app') {{-- Make sure to extend the correct layout --}}

@push('css')
    {{-- <link rel="stylesheet" href="{{ pathAssets('vendor/datatables/css/jquery.dataTables.min.css') }}"> --}}
@endpush

@section('content')
<div class="container">
    <div class="row page-titles trust-wave mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text ">
            <h4>All Notifications</h4>
        </div>
    </div>
    </div>
    <div class="list-group">
        @forelse ($notifications as $notification)

            <a href="#" class="list-group-item list-group-item-action flex-column align-items-start">
                <div class="d-flex w-100 justify-content-between">
                    <h5 class="mb-1">        <span class="success"><i class="ti-image"></i></span>

                        {{ $notification->data['user_name'] ?? 'Unknown User' }}</h5>
                    <small>{{ $notification->created_at->diffForHumans() }}</small>
                </div>
                
                <p class="mb-1">{{ $notification->data['message'] }}</p>
            </a>
        @empty
            <p class="text-center">No notifications found.</p>
        @endforelse
    </div>
</div>
@endsection
