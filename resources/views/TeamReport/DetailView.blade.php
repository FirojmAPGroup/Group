@extends('layouts.app')

@section('content')

<div class="row">
    <!-- Team Details Card -->
    <div class="col">
        <div class="card mb-2">
            <div class="card-header">
                <h4>User Details:</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-xl-6">
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">User Full Name :</label>
                            <div class="col-sm-8">
                                <p class="form-control-plaintext" style="color: #524c4c">{{ $lead->user->first_name }} {{ $lead->user->last_name }}</p>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Email :</label>
                            <div class="col-sm-8">
                                <p class="form-control-plaintext" style="color: #524c4c">{{ $lead->user->email }}</p>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Mobile Number :</label>
                            <div class="col-sm-8">
                                <p class="form-control-plaintext" style="color: #524c4c">{{ $lead->user->phone_number }}</p>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Status :</label>
                            <div class="col-sm-8">
                                <p class="form-control-plaintext" style="color: #524c4c">{!! $lead->leadStatus() !!}</p>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Location Latitude :</label>
                            <div class="col-sm-8">
                                <p class="form-control-plaintext" style="color: #524c4c">{{ $lead->user->latitude ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Location Longitude :</label>
                            <div class="col-sm-8">
                                <p class="form-control-plaintext" style="color: #524c4c">{{ $lead->user->longitude ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Assigned Date :</label>
                            <div class="col-sm-8">
                                <p class="form-control-plaintext" style="color: #524c4c">{{ $lead->created_at->format('Y-m-d') }}</p>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Visit Date :</label>
                            <div class="col-sm-8">
                                <p class="form-control-plaintext" style="color: #524c4c">{{ $lead->visit_date }}</p>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Remark :</label>
                            <div class="col-sm-8">
                                <p class="form-control-plaintext" style="color: #524c4c">{{ $lead->remark ?? 'N/A' }}</p>
                            </div>
                        </div>
                        @if($coordinatesMatch)
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Match Status :</label>
                            <div class="col-sm-8">
                                <div class="alert " role="alert" style="color: #2dd048;font-size:15px">
                                    Lead's location matches with User location!
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Match Status</label>
                            <div class="col-sm-8">
                                <div class="alert " role="alert" style="color: #e02d19;font-size:15px">
                                    Lead's location does not match with User location.
                                </div>
                            </div>
                        </div>
                        @endif
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Selfie :</label>
                            <div class="col-sm-8" id="selfieContainer">
                                <img id="selfieImage" src="{{ asset('/uploads/selfie/' . $lead->selfie) }}" alt="Selfie Image"
                                    style="max-width: 100%; height: auto;" />
                                <p id="imageNotAvailable" style="color: red; text-align: start; display: none;">Image not available</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</div>
<div class="row">
    <!-- Lead Details Card -->
    <div class="col">
        <div class="card mb-2">
            <div class="card-header">
                <h4>Lead Details:</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-xl-6">
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Company Name :</label>
                            <div class="col-sm-8">
                                <p class="form-control-plaintext" style="color: #524c4c">{{ $lead->business->name }}</p>
                            </div>
                        </div>
                       
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Phone Number :</label>
                            <div class="col-sm-8">
                                <p class="form-control-plaintext" style="color: #524c4c">{{ $lead->business->owner_number }}</p>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Area :</label>
                            <div class="col-sm-8">
                                <p class="form-control-plaintext" style="color: #524c4c">{{ $lead->business->area }}</p>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">City :</label>
                            <div class="col-sm-8">
                                <p class="form-control-plaintext" style="color: #524c4c">{{ $lead->business->city }}</p>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">State :</label>
                            <div class="col-sm-8">
                                <p class="form-control-plaintext" style="color: #524c4c">{{ $lead->business->state }}</p>
                            </div>
                        </div>
                       
                    </div>
                    <div class="col-xl-6">
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Full Name :</label>
                            <div class="col-sm-8">
                                <p class="form-control-plaintext" style="color: #524c4c">{{ $lead->business->owner_full_name }}</p>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Email :</label>
                            <div class="col-sm-8">
                                <p class="form-control-plaintext" style="color: #524c4c">{{ $lead->business->owner_email }}</p>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Address :</label>
                            <div class="col-sm-8">
                                <p class="form-control-plaintext" style="color: #524c4c">{{ $lead->business->address }}</p>
                            </div>
                        </div>
                     
                      
                      
                        <div class="form-group row">
                            <div class="col-sm-8 offset-sm-4">
                                <button type="button" class="btn btn-secondary" onclick="history.back()">Back</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

       
@endsection
