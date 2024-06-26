@extends('layouts.app')
@section('content')
<div class="row page-titles trust-wave mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>{{ $heading }} Lead</h4>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="form-validation">
                    <form id="frmLead" class="form-valide cls-crud-simple-save" action="{{ routePut('leads.save') }}" method="post">
                        @csrf
                        <input type="hidden" name="id" value="{{ $business ? encId($business->getId()) : encId(0) }}">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-lg-2 col-form-label" for="name">Company Name 
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-lg-8 col-md-12">
                                        <input type="text" class="form-control" id="name" name="name" placeholder="Company Name" value="{{ old('name', $business->name) }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-lg-2 col-form-label" for="owner_full_name">Lead Full Name
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-lg-8 col-md-12">
                                        <input type="text" class="form-control" id="owner_full_name" name="owner_full_name" placeholder="Lead Full Name" value="{{ old('owner_full_name', $business->owner_full_name) }}" required>
                                    </div>
                                </div>
                            </div>
                           
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-lg-2 col-form-label" for="owner_number">Lead Number
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-lg-8 col-md-12">
                                        <input type="text" class="form-control" id="owner_number" name="owner_number" placeholder="Lead Number" value="{{ old('owner_number', $business->owner_number) }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-lg-2 col-form-label" for="owner_email">Lead Email
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-lg-8 col-md-12">
                                        <input type="email" class="form-control" id="owner_email" name="owner_email" placeholder="Lead Email" value="{{ old('owner_email', $business->owner_email) }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-lg-2 col-form-label" for="country">Country
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-lg-8 col-md-12">
                                        <input type="text" class="form-control" id="country" name="country" placeholder="Country" value="{{ old('country', $business->country) }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-lg-2 col-form-label" for="state">State
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-lg-8 col-md-12">
                                        <input type="text" class="form-control" id="state" name="state" placeholder="State" value="{{ old('state', $business->state) }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-lg-2 col-form-label" for="city">City
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-lg-8 col-md-12">
                                        <input type="text" class="form-control" id="city" name="city" placeholder="City" value="{{ old('city', $business->city) }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-lg-2 col-form-label" for="area">Area
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-lg-8 col-md-12">
                                        <input type="text" class="form-control" id="area" name="area" placeholder="Area" value="{{ old('area', $business->area) }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-lg-2 col-form-label" for="Address">Address
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-lg-8 col-md-12">
                                        <textarea name="Address" id="Address"  placeholder="Address" style="width: 711px; height: 100px; resize:none !important" value="{{ old('Address', $business->address) }}">{{ $business->address }}</textarea>
                                        {{-- <input type="text" class="form-control" id="Address" name="Address" placeholder="Address" value="{{ old('Address', $business->address) }}" required title="Please enter a address"> --}}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-lg-2 col-form-label" for="pincode">Pincode
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-lg-8 col-md-12">
                                        <input type="text" class="form-control" id="pincode" name="pincode" placeholder="Pincode" value="{{ old('pincode', $business->pincode) }}" required pattern="\d{6}" title="Please enter a 6-digit pincode">
                                    </div>
                                </div>
                            </div>
                            {{-- <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-lg-2 col-form-label" for="latitude">Location
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-lg-4 col-md-6 col-sm-12">
                                        <input type="text" class="form-control" id="latitude" name="latitude" placeholder="Latitude" value="{{ old('latitude', $business->latitude) }}" disabled>
                                    </div>
                                    <div class="col-lg-4 col-md-6 col-sm-12">
                                        <input type="text" class="form-control" id="longitude" name="longitude" placeholder="Longitude" value="{{ old('longitude', $business->longitude) }}" disabled>
                                    </div>
                                </div>
                            </div> --}}
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <div class="col-lg-2"></div>
                                    <div class="col-lg-8 ml-auto text-right">
                                        <button type="submit" class="btn trust-wave-button-color">Submit</button>
                                    </div>
                                    <div class="col-lg-2"></div>
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
{{-- <script>
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

  
</script> --}}

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

        // Handle form submission
        $('#frmLead').on('submit', function(event) {
            event.preventDefault(); // Prevent the default form submission

            var formData = $(this).serialize(); // Serialize form data

            $.ajax({
                type: 'POST',
                url: $(this).attr('action'),
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Handle success scenario (redirect or display success message)
                        window.location.href = response.url;
                    } else {
                        // Handle error scenario
                        console.error(response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error(error); // Log the error to console
                }
            });
        });
    });
</script>

@endpush
