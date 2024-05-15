@extends('layouts.app')
@section('content')
<div class="row page-titles trust-wave mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text ">
            <h4>Asign Lead</h4>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="form-validation">
                    <form id="frmSubAdmin" class="form-valide cls-crud-simple-save" action="{{ routePut('leads.submitAsign') }}" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="{{ encId(0) }}">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-lg-2 col-form-label" for="name">Lead
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-lg-8 col-md-12">
                                        <select name="business_id" id="business_id" class="select2">
                                            <option value="" selected>Please select</option>
                                            {!! makeDropdown($leads)  !!}
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-lg-2 col-form-label" for="name">User Name
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-lg-8 col-md-12">
                                        <select name="user_ids" id="user_ids" class="select2">
                                            <option value="" selected>Please select</option>
                                            {!! makeDropdown($user)  !!}
                                        </select>
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
