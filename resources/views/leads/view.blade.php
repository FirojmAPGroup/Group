@extends('layouts.app')
@section('content')
<div class="row page-titles trust-wave mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text ">
            <h4>{{ $heading }} Lead</h4>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="form-validation">
                    <form id="frmLead" class="form-valide cls-crud-simple-save" action="{{ routePut('leads.edit',['id']) }}" method="post">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-lg-2 col-form-label" for="name">Company Name 
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-lg-8 col-md-12">
                                        <input type="text" class="form-control" id="name" name="name" placeholder="Company Name" value="{{ $business->name }}" required disabled>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-lg-2 col-form-label" for="owner_name"> Lead First Name
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-lg-8 col-md-12">
                                        <input type="text" class="form-control" id="owner_first_name" name="owner_first_name" placeholder="Lead First Name" value="{{ $business->owner_first_name }}" disabled required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-lg-2 col-form-label" for="owner_name">Lead Last Name
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-lg-8 col-md-12">
                                        <input type="text" class="form-control" id="owner_last_name" name="owner_last_name" placeholder="Lead Last Name" value="{{ $business->owner_last_name }}" disabled required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-lg-2 col-form-label" for="owner_name">Lead Number
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-lg-8 col-md-12">
                                        <input type="text" class="form-control" id="owner_number" name="owner_number" placeholder="Lead Number" value="{{ $business->owner_number }}" disabled required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-lg-2 col-form-label" for="owner_name">Lead Email
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-lg-8 col-md-12">
                                        <input type="email" class="form-control" id="owner_email" name="owner_email" placeholder="Lead Email" value="{{ $business->owner_email }}" disabled required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-lg-2 col-form-label" for="owner_name">Country
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-lg-8 col-md-12">
                                        <input type="text" class="form-control" id="country" name="country" placeholder="Country" value="{{ $business->country }}" disabled required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-lg-2 col-form-label" for="owner_name">State
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-lg-8 col-md-12">
                                        <input type="text" class="form-control" id="state" name="state" placeholder="State" value="{{ $business->state }}" disabled required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-lg-2 col-form-label" for="owner_name">City
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-lg-8 col-md-12">
                                        <input type="text" class="form-control" id="city" name="city" placeholder="City" value="{{ $business->city }}" disabled required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-lg-2 col-form-label" for="owner_name">Area
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-lg-8 col-md-12">
                                        <input type="text" class="form-control" id="area" name="area" placeholder="Area" value="{{ $business->area }}" disabled required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-lg-2 col-form-label" for="pincode">Pincode
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-lg-8 col-md-12">
                                        <input type="text" class="form-control" id="pincode" name="pincode" placeholder="Pincode" value="{{ $business->pincode }}"  disabledrequired pattern="\d{6}" title="Please enter a 6-digit pincode">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-lg-2 col-form-label" for="pincode">Location
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-lg-4 col-md-6 col-sm-12">
                                        <input type="text" class="form-control" id="latitude" name="latitude" placeholder="Latitude" value="{{ $business->latitude }}" disabled required>
                                    </div>
                                    <div class="col-lg-4 col-md-6 col-sm-12">
                                        <input type="text" class="form-control" id="longitude" name="longitude" placeholder="Longitude" value="{{ $business->longitude }}" disabled required>
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
