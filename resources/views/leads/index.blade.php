{{-- @extends('layouts.app')
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
                                <th data-data="name">Company Name</th>
                                <th data-data="owner_first_name">Lead First Name</th>
                                <th data-data="owner_last_name">Lead Last Name</th>
                                <th data-data="owner_email">Lead Email</th>
                                <th data-data="team">Team member</th>
                                <th data-data="ti_status">Status</th>
                                <th data-data="details">View Detail</th>
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
@endpush --}}

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
    margin: 0 auto;
  }
  .file-name {
    display: block;
    margin-top: 10px;
    font-weight: bold;
  }
</style>
@section('content')
<div class="row page-titles trust-wave mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Leads</h4>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 d-flex justify-content-end">
        <div class="tb-container">
            <button type="button" class="btn mr-2 trust-wave-button-color btn-rounded btn-warning" data-toggle="modal" data-target="#uploadModal">
                <span class="mr-2"><i class="fa fa-upload"></i></span>Upload Bulk Lead
            </button>
        </div>
        <a href="{{ routePut('leads.create') }}" class="btn trust-wave-button-color btn-rounded btn-warning">
            <span class="mr-2"><i class="fa fa-user-plus"></i></span>Create Lead
        </a>
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
                                <th data-data="team">Team member</th>
                                <th data-data="ti_status">Status</th>
                                <th data-data="details">View Detail</th>
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

<!-- Upload Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadModalLabel">Upload Bulk Leads</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Make sure your sheet has the following column names:</p>
                <ul>
                    <li>Company name</li>
                    <li>Owner first name</li>
                    <li>Owner last name</li>
                    <li>Contact</li>
                    <li>email</li>
                    <li>Pincode</li>
                    <li>City</li>
                    <li>State</li>
                    <li>latitude</li>
                    <li>longitude</li>
                    <li>Area</li>
                    <li>Address</li>
                </ul>
                <form id="uploadForm" enctype="multipart/form-data">
                    <div class="tb-container">
                        <label for="bulkupload" class="btn btn-warning btn-rounded">Choose File</label>

                        <input type="file" id="bulkupload" name="file" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" onchange="displayFileName();" />
                    </div>
                    <span id="fileName" class="file-name"></span>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="uploadFile()">Upload</button>
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

    function displayFileName() {
        var fileInput = document.getElementById('bulkupload');
        var fileName = fileInput.files[0].name;
        document.getElementById('fileName').innerText = fileName;
    }

    function uploadFile() {
        var fileInput = $('#bulkupload')[0];
        var file = fileInput.files[0];
        if (file) {
            var formData = new FormData();
            formData.append('file', file);

            $.ajax({
                url: '{{ routePut("leads.bulkUpload") }}',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function(data) {
                    if (data.status) {
                        showAlert(1, data.message);
                        $('#uploadModal').modal('hide');
                        refreshDtTable();
                    } else {
                        showAlert(0, data.message);
                    }
                },
                error: function(xhr, status, error) {
                    var errorMessage = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Something went wrong.';
                    showAlert(0, errorMessage);
                }
            });
        } else {
            showAlert(0, 'Please select a file before uploading.');
        }
    }

    function showAlert(type, message) {
        var alertType = type ? 'alert-success' : 'alert-danger';
        var alertHtml = '<div class="alert ' + alertType + ' alert-dismissible fade show" role="alert">' +
            message +
            '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
            '<span aria-hidden="true">&times;</span>' +
            '</button>' +
            '</div>';
        $('#uploadModal .modal-body').prepend(alertHtml);
    }

    function refreshDtTable() {
        dtTable.ajax.reload();
    }
</script>
@endpush
