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
            <h4>Leads</h4>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 d-flex justify-content-end">
      <form id="uploadForm" enctype="multipart/form-data" style="margin-bottom: 0;">
        <div class="tb-container">
          <label for="bulkupload" class="btn mr-2 trust-wave-button-color btn-rounded btn-warning"><span class="mr-2"><i class="fa fa-upload"></i></span>Upload Bulk Lead</label>
          <input type="file" id="bulkupload" name="bulkupload" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" onchange="uploadFile();" />
        </div>
      </form>
        <a href="{{ routePut('leads.create') }}" class="btn trust-wave-button-color btn-rounded btn-warning"><span class="mr-2"><i class="fa fa-user-plus"></i></span>Create Lead</a>
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
                                <th data-data="name">Business Name</th>
                                <th data-data="owner_first_name">Owner First Name</th>
                                <th data-data="owner_last_name">Owner Last Name</th>
                                <th data-data="owner_email">Owner Email</th>
                                <th data-data="owner_number">Owner Number</th>
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
  function uploadFile() {
      var fileInput = $('#bulkupload')[0];
      var file = fileInput.files[0];
      console.log(file);
      if (file) {
          var formData = new FormData();
          formData.append('file', file);

          $.ajax({
              url: '{{ routePut("leads.bulkUpload") }}',
              type: 'POST',
              data: formData,
              cache: false,
              contentType: false,
              processData: false,
              success: function (data) {
                  // Handle the response from the server
                  console.log(data);
                  if(data.status){
                    showAlert(1, data.message);
                    refreshDtTable();
                  } else{
                    showAlert(0, data.message);
                  }
              },
              error: function (xhr, status, error) {
                var errorMessage = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Something went wrong.';
                    showAlert(0, errorMessage);
              }
          });
      } else {
        showAlert(0, 'Please select a file before uploading.')
      }
  }

  </script>
@endpush
