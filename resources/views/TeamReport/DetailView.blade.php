@extends('layouts.app')

@section('content')

<div class="row">
    <!-- Team Details Card -->
    <div class="col">
        <div class="card mb-2">
            <div class="card-header">
                <h4>Team Details</h4>
            </div>
            <div class="card-body">
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label"> First Name</label>
                    <div class="col-sm-8">
                        <p class="form-control-plaintext" style="color: #524c4c">{{ $lead->user->first_name }}</p>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label"> Last Name</label>
                    <div class="col-sm-8">
                        <p class="form-control-plaintext" style="color: #524c4c">{{ $lead->user->last_name }}</p>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label"> Email</label>
                    <div class="col-sm-8">
                        <p class="form-control-plaintext" style="color: #524c4c">{{ $lead->user->email }}</p>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label"> Mobile Number</label>
                    <div class="col-sm-8">
                        <p class="form-control-plaintext" style="color: #524c4c">{{ $lead->user->phone_number }}</p>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label">Status</label>
                    <div class="col-sm-8">
                        <p class="form-control-plaintext" style="color: #524c4c">{!! $lead->leadStatus() !!}</p>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label">Location Latitude</label>
                    <div class="col-sm-8">
                        <p class="form-control-plaintext" style="color: #524c4c">{{ $lead->latitude ?? 'N/A' }}</p>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label">Location Longitude</label>
                    <div class="col-sm-8">
                        <p class="form-control-plaintext" style="color: #524c4c">{{ $lead->longitude ?? 'N/A' }}</p>
                    </div>
                </div>
                
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label">Assigned Date</label>
                    <div class="col-sm-8">
                        <p class="form-control-plaintext" style="color: #524c4c">{{ $lead->created_at->format('Y-m-d') }}</p>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label">Visit Date</label>
                    <div class="col-sm-8">
                        <p class="form-control-plaintext" style="color: #524c4c">{{ $lead->visit_date }}</p>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label">Remark</label>
                    <div class="col-sm-8">
                        <p class="form-control-plaintext" style="color: #524c4c">{{ $lead->remark ?? 'N/A' }}</p>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label">Selfie</label>
                    <div class="col-sm-8" id="selfieContainer" >
                        <img id="selfieImage" src="{{ asset('/uploads/selfie/' . $lead->selfie) }}" alt="Selfie Image"
                            style="max-width: 100%; color: #524c4c; height: 200px; display: none;" />
                        <p id="imageNotAvailable" style="color: red; text-align: start; display: none;">Image not available</p>
                    </div>
                </div>
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        var selfieImage = document.getElementById('selfieImage');
                        var imageNotAvailable = document.getElementById('imageNotAvailable');
                
                        selfieImage.onload = function () {
                            // Image loaded successfully
                            selfieImage.style.display = 'block';
                            imageNotAvailable.style.display = 'none';
                        };
                
                        selfieImage.onerror = function () {
                            // Image failed to load
                            selfieImage.style.display = 'none';
                            imageNotAvailable.style.display = 'block';
                        };
                
                        // Check if image is loaded successfully
                        if (selfieImage.complete && selfieImage.naturalWidth !== 0) {
                            selfieImage.style.display = 'block';
                            imageNotAvailable.style.display = 'none';
                        } else {
                            selfieImage.style.display = 'none';
                            imageNotAvailable.style.display = 'block';
                        }
                    });
                </script>
                
                
            </div>
        </div>
    </div>
</div>

        <div class="row">
            <!-- Lead Details Card -->
                <div class="col">
                    <div class="card-header">
                        <h4>Lead Details</h4>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Company Name</label>
                            <div class="col-sm-8">
                                <p class="form-control-plaintext" style="color: #524c4c">{{ $lead->business->name }}</p>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label"> First Name</label>
                            <div class="col-sm-8">
                                <p class="form-control-plaintext" style="color: #524c4c">{{ $lead->business->owner_first_name }}</p>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label"> Last Name</label>
                            <div class="col-sm-8">
                                <p class="form-control-plaintext" style="color: #524c4c">{{ $lead->business->owner_last_name }}</p>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label"> Phone Number</label>
                            <div class="col-sm-8">
                                <p class="form-control-plaintext" style="color: #524c4c">{{ $lead->business->owner_number }}</p>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label"> Email</label>
                            <div class="col-sm-8">
                                <p class="form-control-plaintext" style="color: #524c4c">{{ $lead->business->owner_email }}</p>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label"> Area</label>
                            <div class="col-sm-8">
                                <p class="form-control-plaintext" style="color: #524c4c">{{ $lead->business->area }}</p>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label"> City</label>
                            <div class="col-sm-8">
                                <p class="form-control-plaintext" style="color: #524c4c">{{ $lead->business->city }}</p>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label"> State</label>
                            <div class="col-sm-8">
                                <p class="form-control-plaintext" style="color: #524c4c">{{ $lead->business->state }}</p>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-0 text-right">
                                <button type="button" class="btn btn-secondary" onclick="history.back()">Back</button>
                            </div>
                        </div>
                    </div>
            </div>
        </div>
        
@endsection
