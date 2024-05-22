@extends('layouts.app')
@push('css')
@endpush
@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>{{ $title }} </h4>
        </div>
    </div>
</div>
<!-- row -->
<div class="row">
    <div class="col-xl-12 col-xxl-12">
        <div class="card">
            <div class="card-body">
                <form id="frmSubAdmin" class="form-valide cls-crud-simple-save" action="{{ routePut('pages.contact-us') }}" method="post">
                    <div class="form-group row">
                        <label class="col-md-2 col-form-label" for="mobile_number">Mobile Number
                            <span class="text-danger">*</span>
                        </label>
                        <div class="col-md-10">
                            <input type="tel" name="mobile_number" id="mobile_number" class="form-control" value="{!! $page->mobile_number !!}" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-2 col-form-label" for="email">Email
                            <span class="text-danger">*</span>
                        </label>
                        <div class="col-md-10">
                            <input type="email" name="email" id="email" class="form-control" value="{!! $page->email !!}" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-2 col-form-label" for="address" required>Location
                            <span class="text-danger">*</span>
                        </label>
                        <div class="col-md-10">
                            <textarea name="address" id="address" class="form-control">{!! $page->content !!}</textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col md-auto">
                            <a href="{{ route('app.dashboard') }}" class="btn btn-secondary  ">Back</a>
                        </div>
                        <div class="col-lg-8 ml-auto text-right">
                            <button type="submit" class="btn trust-wave-button-color">Update</button>
                        </div>
                    </div>
                </form>

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
</script>
@endpush
