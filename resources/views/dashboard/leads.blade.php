@extends('layouts.app')

@push('css')
    <link rel="stylesheet" href="{{ pathAssets('vendor/datatables/css/jquery.dataTables.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.css">
@endpush

@section('content')
<div class="row page-titles trust-wave mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text ">
            <h4>{{ $title }}</h4>
        </div>
    </div>
    <div class="col p-md-0 d-flex justify-content-end" >
        <div class="dropdown">
            <button class="btn btn-secondary dropdown-toggle" type="button" id="teamMemberDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Select Team Member
            </button>
            <div class="dropdown-menu" aria-labelledby="teamMemberDropdown" style="max-height: 200px; overflow-y: auto;">
                @foreach($teamMembers as $member)
                <a class="dropdown-item" href="#" data-member-id="{{ $member->id }}">{{ $member->first_name }} {{ $member->last_name }}</a>
                @endforeach
            </div>
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
                                <th data-data="owner_full_name">Lead Full Name</th>
                                <th data-data="owner_email">Lead Email</th>
                                <th data-data="owner_number">Lead Number</th>
                                <th data-data="ti_status">Status</th>
                                <th data-data="user_full_name">Team Full Name</th>
                                <th data-data="details" data-sortable="false">Details</th>
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
        var dtTable;
        var selectedMemberName = '';

        // Function to reload DataTable with selected team member
        function reloadDataTable(memberId) {
            dtTable.ajax.url('{{ $urlListData }}' + '?member_id=' + memberId).load();
        }

        // Function to update selected member name in the dropdown button
        function updateSelectedMemberName(memberName) {
            selectedMemberName = memberName;
            $('#teamMemberDropdown').html(selectedMemberName);
        }

        // Initialize DataTable
        dtTable = applyDataTable('#{{ $table }}', '{{ $urlListData }}', {});

        // Handle team member selection
        $('.dropdown-item').click(function(e) {
            e.preventDefault();
            var memberId = $(this).data('member-id');
            var memberName = $(this).text();
            updateSelectedMemberName(memberName);
            reloadDataTable(memberId);
        });
    });
</script>

@endpush
