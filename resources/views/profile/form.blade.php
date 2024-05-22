@extends('layouts.app')
@push('css')
    <link rel="stylesheet" href="{{ pathAssets('vendor/datatables/css/jquery.dataTables.min.css') }}">
@endpush
@section('content')
    <div class="row page-titles trust-wave mx-0">
        <div class="col-sm-6 p-md-0 ">
            <div class="welcome-text ">
                <h4>{{ $heading }}</h4>
            </div>
        </div>
        <!-- Right Side: Dropdown Menu -->
        <div class="col-sm-6 p-md-0 d-flex justify-content-end mt-0">
            <ul class="navbar-nav header-right">
                <li class="nav-item dropdown notification_dropdown" style="top:-10px !important">
                    <a class="nav-link  trust-wave-buton-edit" href="#" role="button" data-toggle="dropdown">
                        {{-- <i class="mdi mdi-bell"></i> --}}
                        <i class="fa fa-pencil color-muted "></i> 
                    </a>
                    <div class="dropdown-menu dropdown-menu-left" style="padding: 10px">
                        <!-- Dropdown items go here -->
                        <a href="{{ route('profile.edit', ['id' => $user->id]) }}">
                            <i class="icon-user"></i>
                            <span class="ml-2">Update Profile</span>
                        </a>
                    </div>
                </li>
            </ul>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">View Profile</h4>
                </div>

                <div class="card-body">
                    <div class="form-validation">
                        <form id="frmadmin" class="form-valide cls-crud-simple-save" method="post"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-xl-6">
                                    <div class="form-group row">
                                        <label class="col-lg-4 col-form-label" for="first_name">First Name
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div class="col-lg-6">
                                            <input type="text" class="form-control" id="first_name" name="first_name"
                                                placeholder="First Name" value="{{ $user->first_name ?? '' }}" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-lg-4 col-form-label" for="email">Email <span
                                                class="text-danger">*</span>
                                        </label>
                                        <div class="col-lg-6">
                                            <input type="email" class="form-control" id="email" name="email"
                                                placeholder="Your valid email.." required value="{{ $user->email ?? '' }}"
                                                readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-lg-4 col-form-label" for="phone">Phone Number <span
                                                class="text-danger">*</span>
                                        </label>
                                        <div class="col-lg-6">
                                            <input type="text" class="form-control" id="phone" name="phone"
                                                placeholder="Phone Number" required value="{{ $user->phone_number ?? '' }}"
                                                readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-lg-4 col-form-label" for="birth_date">Date of Birth
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div class="col-lg-6">
                                            <input type="date" class="form-control" id="birth_date" name="birth_date"
                                                placeholder="Date of Birth " value="{{ $user->birth_date ?? '' }}" required
                                                readonly>
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
                                                placeholder="Last Name" value="{{ $user->last_name ?? '' }}" required
                                                readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-lg-4 col-form-label" for="admin_title">Admin Title
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div class="col-lg-6">
                                            <select class="form-control select2" id="admin_title" name="admin_title"
                                                required disabled>
                                                <option value="" selected>Please select</option>
                                                {!! makeDropdown(siteConfig('title'), $user->title) !!}
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-lg-4 col-form-label" for="gender">Gender
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div class="col-lg-6">
                                            <select class="form-control select2" id="gender" name="gender" required
                                                disabled>
                                                <option value="" disabled selected>Select Gender</option>
                                                <option value="male"
                                                    {{ isset($user) && $user->gender == 'male' ? 'selected' : '' }}>Male
                                                </option>
                                                <option value="female"
                                                    {{ isset($user) && $user->gender == 'female' ? 'selected' : '' }}>
                                                    Female</option>
                                                <option value="other"
                                                    {{ isset($user) && $user->gender == 'other' ? 'selected' : '' }}>
                                                    Other</option>
                                            </select>
                                        </div>
                                    </div>

                                    {{-- <div class="form-group row">
                                        <div class="col-lg-8 ml-auto">
                                            <button type="submit" onclick="redirect()"
                                                class="btn trust-wave-button-color">Update</button>
                                        </div>

                                    </div> --}}
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
@endpush

@push('script')
    <script>
        // Toggle password visibility
        $('#toggle-password').click(function() {
            var input = $('#password');
            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                $(this).find('i').removeClass('mdi mdi-eye-outline').addClass('mdi mdi-eye-off-outline');
            } else {
                input.attr('type', 'password');
                $(this).find('i').removeClass('mdi mdi-eye-off-outline').addClass('mdi mdi-eye-outline');
            }
        });
    </script>
@endpush
