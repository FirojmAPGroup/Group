@extends('layouts.app')

@push('css')
    <link rel="stylesheet" href="{{ pathAssets('vendor/datatables/css/jquery.dataTables.min.css') }}">
    <style>
        .popup-message {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #28a745;
            color: white;
            padding: 15px;
            border-radius: 0px;
            display: none;
            z-index: 1000;
        }
    </style>
@endpush

@section('content')
    <div class="row page-titles trust-wave mx-0">
        <div class="col-sm-6 p-md-0 ">
            <div class="welcome-text ">
                <h4>Profile update</h4>
            </div>
        </div>
        {{-- right side content   --}}
        <div class="col-sm-6 p-md-0 d-flex justify-content-end mt-0">
            <ul class="navbar-nav header-right">
                <li class="nav-item dropdown notification_dropdown" style="top:-10px !important">
                    <a class="nav-link  trust-wave-buton-edit" href="#" role="button" data-toggle="dropdown">
                        {{-- <i class="mdi mdi-bell"></i> --}}
                        <span>
                            <a href="{{ route('profile.updatePassword', $user->id) }}" >
                                <i class="icon-key"></i>
                                <span class="ml-2">Change Password</span>
                            </a>
                        </span>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="popup-message" id="popupMessage">Profile updated successfully!</div>
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="form-validation">
                    <form id="frmadmin" class="form-valide cls-crud-simple-save"
                        action="{{ route('profile.update', ['id' => $user->id]) }}" method="post"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-xl-6">
                                <div class="form-group row">
                                    <label class="col-lg-4 col-form-label" for="first_name">First Name
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-lg-6">
                                        <input type="text" class="form-control" id="first_name" name="first_name"
                                            placeholder="First Name" value="{{ $user->first_name ?? '' }}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-4 col-form-label" for="email">Email <span
                                            class="text-danger">*</span>
                                    </label>
                                    <div class="col-lg-6">
                                        <input type="email" class="form-control" id="email" name="email"
                                            placeholder="Your valid email.." required value="{{ $user->email ?? '' }}"
                                            disabled>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-4 col-form-label" for="phone">Phone Number <span
                                            class="text-danger">*</span>
                                    </label>
                                    <div class="col-lg-6">
                                        <input type="text" class="form-control" id="phone" name="phone"
                                            placeholder="Phone Number" required value="{{ $user->phone_number ?? '' }}"
                                            disabled>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-4 col-form-label" for="gender">Gender
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-lg-6">
                                        <select class="form-control select2" id="gender" name="gender" required>
                                            <option value="" disabled selected>Select Gender</option>
                                            <option value="male"
                                                {{ isset($user) && $user->gender == 'male' ? 'selected' : '' }}>Male
                                            </option>
                                            <option value="female"
                                                {{ isset($user) && $user->gender == 'female' ? 'selected' : '' }}>
                                                Female</option>
                                            <option value="other"
                                                {{ isset($user) && $user->gender == 'other' ? 'selected' : '' }}>Other
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-6">
                                <div class="form-group row">
                                    <label class="col-lg-4 col-form-label" for="last_name">Last Name
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-lg-6">
                                        <input type="text" class="form-control" id="last_name" name="last_name"
                                            placeholder="Last Name" value="{{ $user->last_name ?? '' }}" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-4 col-form-label" for="admin_title">Admin Title
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-lg-6">
                                        <select class="form-control select2" id="admin_title" name="admin_title" required>
                                            <option value="" selected>Please select</option>
                                            {!! makeDropdown(siteConfig('title'), $user->title) !!}
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-4 col-form-label" for="birth_date">Date of Birth
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-lg-6">
                                        <input type="date" class="form-control" id="birth_date" name="birth_date"
                                            placeholder="Date of Birth " value="{{ $user->birth_date ?? '' }}" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-lg-8 ml-auto">
                                        <button type="submit" onclick="redirectToDashboard()"
                                            class="btn trust-wave-button-color">Update</button>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>

   
@endsection

@push('js')
    <script src="{{ pathAssets('vendor/jquery-validation/jquery.validate.min.js') }}"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
@endpush

@push('script')

    <script>
        $(document).ready(function() {
            $('#frmadmin').on('submit', function(event) {
                event.preventDefault();

                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.success) {
                            $('#popupMessage').fadeIn();
                            setTimeout(function() {
                                $('#popupMessage').fadeOut(function() {
                                    window.location.href =
                                        "{{ route('app.dashboard') }}";
                                });
                            }, 3000); // Hide after 3 seconds
                        }
                    },
                    error: function(response) {
                        alert('An error occurred while updating the profile.');
                    }
                });
            });

            document.addEventListener('DOMContentLoaded', function() {
                // Get all input fields
                var inputs = document.querySelectorAll('input[type="text"], textarea');

                // Function to capitalize the first letter of a string
                function capitalizeFirstLetter(str) {
                    return str.charAt(0).toUpperCase() + str.slice(1);
                }

                // Loop through each input field
                inputs.forEach(function(input) {
                    input.addEventListener('input', function() {
                        this.value = capitalizeFirstLetter(this.value);
                    });
                });
            });
        });
    </script>
@endpush
