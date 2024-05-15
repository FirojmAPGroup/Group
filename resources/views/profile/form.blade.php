@extends('layouts.app')

@push('css')
    <link rel="stylesheet" href="{{ pathAssets('vendor/datatables/css/jquery.dataTables.min.css') }}">
@endpush

<style>
    .field-icon {
        display: block;
        float: right !important;
        margin-left: 205px;
        margin-right: 0px;
        margin-top: -28px;
        position: relative;
        z-index: 2;
    }
</style>

@section('content')
<div class="row page-titles trust-wave mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text ">
            <h4>{{ $heading }}</h4>
        </div>
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
                    <form id="frmadmin" class="form-valide cls-crud-simple-save" action="{{ route('profile.save') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-xl-6">
                                <div class="form-group row">
                                    <label class="col-lg-4 col-form-label" for="first_name">First Name
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-lg-6">
                                        <input type="text" class="form-control" id="first_name" name="first_name" placeholder="First Name" value="{{ $user->first_name ?? '' }}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-4 col-form-label" for="email">Email <span
                                            class="text-danger">*</span>
                                    </label>
                                    <div class="col-lg-6">
                                        <input type="email" class="form-control" id="email" name="email" placeholder="Your valid email.." required value="{{ $user->email ?? '' }}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-4 col-form-label" for="phone">Phone Number <span
                                            class="text-danger">*</span>
                                    </label>
                                    <div class="col-lg-6">
                                        <input type="text" class="form-control" id="phone" name="phone" placeholder="Phone Number"  required value="{{ $user->phone_number ?? '' }}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-lg-8 ml-auto">
                                        <a href="{{ route('app.dashboard') }}" class="btn btn-secondary">Back</a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-6">
                                <div class="form-group row">
                                    <label class="col-lg-4 col-form-label" for="last_name">Last Name
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-lg-6">
                                        <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Last Name" value="{{ $user->last_name ?? '' }}" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-4 col-form-label" for="admin_title">Admin Title
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-lg-6">
                                        <select class="form-control select2" id="admin_title" name="admin_title" required>
                                            <option value="" selected>Please select</option>
                                            {!! makeDropdown(siteConfig('title'),$user->title) !!}
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-4 col-form-label" for="password">Password
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-lg-6">
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="password" name="password" placeholder="Enter your new password" required value="{{  '' }}">
                                            <span class="col-lg-6 field-icon" id="toggle-password">
                                                <i class="mdi mdi-eye-outline toggle-password"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group row">
                                    <div class="col-lg-8 ml-auto">
                                        <button type="submit" onclick="redirect()" class="btn trust-wave-button-color">Update</button>
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
@endpush

@push('script')
<script>
    // Toggle password visibility
    $('#toggle-password').click(function(){
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
<script>
    // Function to capitalize the first letter of a string
    function capitalizeFirstLetter(string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    }
    // Function to capitalize the first letter of the input field before form submission
    function capitalizeInputFields() {
        // Get the input fields by their IDs
        var firstNameInput = document.getElementById('first_name');
        var lastNameInput = document.getElementById('last_name');
        // Capitalize the first letter of the input fields
        firstNameInput.value = capitalizeFirstLetter(firstNameInput.value);
        lastNameInput.value = capitalizeFirstLetter(lastNameInput.value);
    }

    // Attach an event listener to the form submission event
    document.getElementById('frmadmin').addEventListener('submit', function(event) {
        capitalizeInputFields();
    });
</script>
<script>
   function redirect() {
        setTimeout(function() {
            window.location.href = "{{ routePut('app.dashboard') }}";
        }, 2000);
    }
</script>
@endpush
