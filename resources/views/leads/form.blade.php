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
                    <form id="frmLead" class="form-valide cls-crud-simple-save" action="{{ routePut('leads.save') }}" method="post">
                        <input type="hidden" name="id" value="{{ $business ? encId($business->getId()) : encId(0) }}">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-lg-2 col-form-label" for="name">Name
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-lg-8 col-md-12">
                                        <input type="text" class="form-control" id="name" name="name" placeholder="Name" value="{{ $business->name }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-lg-2 col-form-label" for="owner_name">Owner Name
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-lg-8 col-md-12">
                                        <input type="text" class="form-control" id="owner_name" name="owner_name" placeholder="Owner Name" value="{{ $business->owner_name }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-lg-2 col-form-label" for="owner_name">Owner Number
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-lg-8 col-md-12">
                                        <input type="text" class="form-control" id="owner_number" name="owner_number" placeholder="Owner Number" value="{{ $business->owner_number }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-lg-2 col-form-label" for="owner_name">Owner Email
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-lg-8 col-md-12">
                                        <input type="email" class="form-control" id="owner_email" name="owner_email" placeholder="Owner Email" value="{{ $business->owner_email }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-lg-2 col-form-label" for="owner_name">Country
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-lg-8 col-md-12">
                                        <input type="text" class="form-control" id="country" name="country" placeholder="Country" value="{{ $business->country }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-lg-2 col-form-label" for="owner_name">State
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-lg-8 col-md-12">
                                        <input type="text" class="form-control" id="state" name="state" placeholder="State" value="{{ $business->state }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-lg-2 col-form-label" for="owner_name">City
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-lg-8 col-md-12">
                                        <input type="text" class="form-control" id="city" name="city" placeholder="City" value="{{ $business->city }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-lg-2 col-form-label" for="owner_name">Area
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-lg-8 col-md-12">
                                        <input type="text" class="form-control" id="area" name="area" placeholder="Area" value="{{ $business->area }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-lg-2 col-form-label" for="pincode">Pincode
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-lg-8 col-md-12">
                                        <input type="text" class="form-control" id="pincode" name="pincode" placeholder="Pincode" value="{{ $business->pincode }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-lg-2 col-form-label" for="pincode">Location
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-lg-4 col-md-6 col-sm-12">
                                        <input type="text" class="form-control" id="latitude" name="latitude" placeholder="Latitude" value="{{ $business->latitude }}" required>
                                    </div>
                                    <div class="col-lg-4 col-md-6 col-sm-12">
                                        <input type="text" class="form-control" id="longitude" name="longitude" placeholder="Longitude" value="{{ $business->longitude }}" required>
                                    </div>
                                </div>
                            </div>
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
<script>

</script>
@endpush
