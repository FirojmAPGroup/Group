@extends('layouts.app')

@push('css')
    <link rel="stylesheet" href="{{ pathAssets('vendor/datatables/css/jquery.dataTables.min.css') }}">
@endpush

@section('content')
    <!-- Password Change Modal -->
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form action="{{ route('profile.changePassword', $user->id) }}" method="POST">
                    @csrf
                    <div class="form-group row">
                        <label class="col-lg-4 col-form-label" for="current_password">Current Password
                            <span class="text-danger">*</span>
                        </label>
                        <div class="col-lg-6">
                            <input type="password" placeholder="Current Password" class="form-control" id="current_password" name="current_password" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-4 col-form-label" for="new_password">New Password
                            <span class="text-danger">*</span>
                        </label>
                        <div class="col-lg-6">
                            <input type="password" class="form-control" id="new_password" name="new_password" placeholder="New Password" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-4 col-form-label" for="password_confirmation">Confirm Password
                            <span class="text-danger">*</span>
                        </label>
                        <div class="col-lg-6">
                            <input type="password" class="form-control" id="password_confirmation" name="new_password_confirmation" placeholder="Confirm Password" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Change Password</button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="{{ pathAssets('vendor/jquery-validation/jquery.validate.min.js') }}"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
@endpush
