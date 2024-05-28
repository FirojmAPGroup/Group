@extends('layouts.app')
@push('css')
    <link rel="stylesheet" href="{{ pathAssets('vendor/datatables/css/jquery.dataTables.min.css') }}">
@endpush
@section('content')
<div class="row page-titles trust-wave mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text ">
            <h4>{{ $heading }} Sub Admin</h4>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Form Validation</h4>
            </div>
            <div class="card-body">
                <div class="form-validation">
                    <form id="frmSubAdmin" class="form-valide cls-crud-simple-save" action="{{ routePut('subadmin.save') }}" method="post">
                        <input type="hidden" name="id" value="{{ $user ? encId($user->getId()) : encId(0) }}">
                        <div class="row">
                            <div class="col-xl-6">
                                <div class="form-group row">
                                    <label class="col-lg-4 col-form-label" for="first_name">First Name
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-lg-6">
                                        <input type="text" class="form-control" id="first_name" name="first_name" placeholder="First Name" value="{{ $user->first_name }}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-4 col-form-label" for="email">Email <span
                                            class="text-danger">*</span>
                                    </label>
                                    <div class="col-lg-6">
                                        <input type="email" class="form-control" id="email" name="email" placeholder="Your valid email.." required value="{{ $user->email }}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-4 col-form-label" for="phone">Phone Number <span
                                            class="text-danger">*</span>
                                    </label>
                                    <div class="col-lg-6">
                                        <input type="number" class="form-control" id="phone" name="phone" placeholder="Phone Number" value="{{ $user->phone_number }}" required oninput="limitLength(this, 10)">
                                    </div>
                                </div>
                                @if(!$user->getId())
                                <div class="form-group row">
                                    <label class="col-lg-4 col-form-label" for="password">Password
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-lg-6">
                                        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                                    </div>
                                </div>
                                @endif
                            </div>
                            <div class="col-xl-6">
                                <div class="form-group row">
                                    <label class="col-lg-4 col-form-label" for="last_name">Last Name
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-lg-6">
                                        <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Last Name" value="{{ $user->last_name }}" required>
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
                                    <label class="col-lg-4 col-form-label" for="permission">Permission
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-lg-6">
                                        <select id="permission" name="permission[]" multiple="multiple" class="form-control select2" required>
                                            {!! makeDropdown($permission,$selectedPermission) !!}
                                        </select>
                                    </div>
                                </div>
                                @if(!$user->getId())
                                <div class="form-group row">
                                    <label class="col-lg-4 col-form-label" for="password_confirmation"> Confirm Password
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-lg-6">
                                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Confirm Password" required>
                                    </div>
                                </div>
                                @endif
                                <div class="form-group row">
                                    <div class="col-lg-8 ml-auto">
                                        <button type="submit" class="btn trust-wave-button-color">Submit</button>
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
@stop
@push('js')
    <script src="{{ pathAssets('vendor/jquery-validation/jquery.validate.min.js') }}"></script>
@endpush
@push('script')
<script>
    function limitLength(element, maxLength) {
        let value = element.value;
        if (value.length <= maxLength) {
            element.value = value;
            if (!$(element).next().hasClass('error')) {
                $(element).after('<label class="error" style="dispaly:block;">this field should be extact '+maxLength+' digits</label>');
            }
        } else if(value.length >= maxLength) {
            element.value = value.slice(0, maxLength);
            if (!$(element).next().hasClass('error')) {
                $(element).after('<label class="error" style="dispaly:block;">this field should be extact '+maxLength+' digits</label>');
            }
        } else {
            $(element). $(element).after('');
        }
  }
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
    document.querySelector('form').addEventListener('submit', function() {
    this.querySelector('button[type="submit"]').disabled = true;
});
</script>
@endpush
