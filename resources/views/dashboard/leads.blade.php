@extends('layouts.app')
@push('css')
    <link rel="stylesheet" href="{{ pathAssets('vendor/datatables/css/jquery.dataTables.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.css">
@endpush
<style>
  .tb-container input {
  display: none;
}
.tb-container label {
  margin:0 auto;
}

</style>
@section('content')
<div class="row page-titles trust-wave mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text ">
            <h4>{{ $title }}</h4>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="{{ $table }}" class="display" style="width:100%">
                        <thead>
                            <tr>
                                <th data-data="name">Company Name</th>
                                <th data-data="owner_first_name">Lead First Name</th>
                                <th data-data="owner_last_name">Lead Last Name</th>
                                <th data-data="owner_email">Lead Email</th>
                                <th data-data="owner_number">Lead Number</th>
                                <th data-data="pincode">Pincode</th>
                                <th data-data="area">Area</th>
                                <th data-data="city">City</th>
                                <th data-data="state">State</th>
                                <th data-data="country">Country</th>
                                <th data-data="ti_status">Status</th>
                                <th data-data="created_at">Created at</th>
                                <th data-data="actions" data-sortable="false">Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
@push('js')
    <script src="{{ pathAssets('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js"></script>

@endpush
@push('script')
<script>
    jQuery(document).ready(function() {
      dtTable = applyDataTable('#{{$table}}', '{!! $urlListData ?? "" !!}', {});
    });
  </script>
@endpush
