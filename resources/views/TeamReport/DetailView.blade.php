@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 >Details</h3>
                </div>
               
                <div class="card-body">
                    <div class="form-validation">
                        <form id="frmadmin" class="form-valide cls-crud-simple-save" method="post"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-xl-6">
                                    <h4>Lead Details:-</h4>
                                    <div class="form-group row">
                                        <label class="col-lg-4 col-form-label" for="first_name">Company Name
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div class="col-lg-6">
                                            <input type="text" class="form-control" id="first_name" name="first_name"
                                                placeholder="First Name" value="{{ $lead->business->name }}" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-lg-4 col-form-label" for="first_name">Lead First Name
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div class="col-lg-6">
                                            <input type="text" class="form-control" id="" name=""
                                                placeholder="First Name" value="{{ $lead->business->owner_first_name }}"
                                                readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-lg-4 col-form-label" for="first_name">Lead Last Name
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div class="col-lg-6">
                                            <input type="text" class="form-control" id="" name=""
                                                placeholder="First Name" value=" {{ $lead->business->owner_last_name }}"
                                                readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-lg-4 col-form-label" for="phone">Lead Phone Number <span
                                                class="text-danger">*</span>
                                        </label>
                                        <div class="col-lg-6">
                                            <input type="text" class="form-control" id="phone" name="phone"
                                                placeholder="Phone Number" required
                                                value="{{ $lead->business->owner_number }}" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-lg-4 col-form-label" for="phone">Lead Email <span
                                                class="text-danger">*</span>
                                        </label>
                                        <div class="col-lg-6">
                                            <input type="text" class="form-control" id="phone" name="phone"
                                                placeholder="Phone Number" required
                                                value="{{ $lead->business->owner_email }}" readonly>
                                        </div>
                                    </div>
                                 
                                    <div class="form-group row">
                                        <div class="col-lg-6 ">
                                            <button type="button" class="btn btn-secondary" onclick="history.back()">Back</button>
                                        </div>
                                    </div>


                                </div>

                                <div class="col-xl-6">
                                    <h4>Team Details:-</h4>

                                    <div class="form-group row">
                                        <label class="col-lg-4 col-form-label" for="last_name">Team First Name
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div class="col-lg-6">
                                            <input type="text" class="form-control" id="" name=""
                                                placeholder=" Name" value="{{ $lead->user->first_name }}" required
                                                readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-lg-4 col-form-label" for="">Team Last Name
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div class="col-lg-6">
                                            <input type="text" class="form-control" id="" name=""
                                                placeholder=" Name" value="{{ $lead->user->last_name }}" required
                                                readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-lg-4 col-form-label" for="">Team Email
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div class="col-lg-6">
                                            <input type="text" class="form-control" id="" name=""
                                                placeholder=" Name" value="{{ $lead->user->email }}" required readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-lg-4 col-form-label" for="">Team Mobile Number
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div class="col-lg-6">
                                            <input type="text" class="form-control" id="" name=""
                                                placeholder=" Name" value="{{ $lead->user->phone_number }}" required
                                                readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-lg-4 col-form-label" for="admin_title"> Status
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div class="col-lg-6">
                                            {!! $lead->leadStatus() !!}
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-lg-4 col-form-label" for="phone">Assigned Date <span
                                                class="text-danger">*</span>
                                        </label>
                                        <div class="col-lg-6">
                                            <input type="text" class="form-control" id="phone" name="phone"
                                                placeholder="Phone Number" required
                                                value="{{ $lead->created_at->format('Y-m-d') }}" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-lg-4 col-form-label" for="phone">Visit Date <span
                                                class="text-danger">*</span>
                                        </label>
                                        <div class="col-lg-6">
                                            <input type="text" class="form-control" id="phone" name="phone"
                                                placeholder="Phone Number" required value="{{ $lead->visit_date }}"
                                                readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-lg-4 col-form-label" for="phone">Remark<span
                                                class="text-danger">*</span>
                                        </label>
                                        <div class="col-lg-6">
                                            <input type="text" class="form-control" id="phone" name="phone"
                                                placeholder="Remark" required value="{{ $lead->remark }}" readonly>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-lg-4 col-form-label" for="phone">Selfie
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div class="col-lg-6" id="selfieContainer">
                                            <img src="{{ asset('/uploads/selfie/' . $lead->selfie) }}" alt="Selfie Image"
                                                style="max-width: 100%;" height="200px" onerror="showImageNotFoundMessage()" />
                                        </div>
                                    </div>

                                    <script>
                                        function showImageNotFoundMessage() {
                                            var container = document.getElementById('selfieContainer');
                                            container.innerHTML = 'Image not available';
                                            container.style.textAlign = 'center';
                                            container.style.color = 'red';
                                        }
                                    </script>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
