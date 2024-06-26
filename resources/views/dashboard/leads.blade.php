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
                Select User Member
            </button>
            <div class="dropdown-menu" aria-labelledby="teamMemberDropdown" style="max-height: 200px; overflow-y: auto;">
                <a class="dropdown-item" href="#" data-member-id="all">Show All</a>
                @foreach($teamMembers as $member)
                <a class="dropdown-item" href="#" data-member-id="{{ $member->id }}">{{ $member->first_name }} {{ $member->last_name }}</a>
                @endforeach

            </div>
              <!-- Export Form -->
            <form id="exportForm" method="GET" action="{{ route('export.leads') }}" class="d-inline">
                <input type="hidden" name="status" value="{{ request('status') }}">
                <input type="hidden" name="member_id" id="exportMemberId" value="">
                <button type="submit" class="btn btn-primary">Export to Excel</button>
            </form>
        
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
                                <th data-data="name" style="width: 20%;">Company Name</th>
                                <th data-data="owner_full_name" style="width: 20%;"> Full Name</th>
                                <th data-data="owner_number" style="width: 15%;"> Number</th>
                                <th data-data="ti_status" style="width: 15%;">Status</th>
                                <th data-data="user_full_name" style="width: 20%;">User Full Name</th>
                                <th data-data="details" data-sortable="false" style="width: 10%;">Details</th>
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

@push('script')

{{-- <script>
    
    jQuery(document).ready(function() {
        var dtTable;
        var selectedMemberName = '';

        // Function to reload DataTable with selected team member
        function reloadDataTable(memberId) {
            var url = '{{ $urlListData }}';
            if (memberId !== 'all') {
                url += '?member_id=' + memberId;
            }
            dtTable.ajax.url(url).load();
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

        $('table').on('draw.dt', function () {
            $('td[data-data="owner_full_name"]').each(function () {
                var text = $(this).text();
                if (text.length > 20) {
                    $(this).text(text.substring(0, 20) + '...'); // Adjusted to show up to 20 characters
                }
            });
        });

    });

</script> --}}
<script>
    jQuery(document).ready(function() {
        var dtTable;
        var selectedMemberName = '';
    
        // Function to reload DataTable with selected team member
        function reloadDataTable(memberId) {
            var url = '{{ $urlListData }}';
            if (memberId !== 'all') {
                url += '?member_id=' + memberId;
            }
            dtTable.ajax.url(url).load();
        }
    
        // Function to update selected member name in the dropdown button
        function updateSelectedMemberName(memberName) {
            selectedMemberName = memberName;
            $('#teamMemberDropdown').html(selectedMemberName);
        }
    
        // Initialize DataTable with search placeholder
        dtTable = applyDataTable('#{{ $table }}', '{{ $urlListData }}', {
            "initComplete": function () {
                this.api().columns().every(function () {
                    var column = this;
                    var headerText = $(column.header()).text().trim();
                    $(column.footer()).html('<input type="text" placeholder="Search ' + headerText + '" />');
    
                    // Apply search functionality
                    $('input', column.footer()).on('keyup change', function () {
                        if (column.search() !== this.value) {
                            column.search(this.value).draw();
                        }
                    });
                });
    
                // Add placeholder to main search input
                $('.dataTables_filter input').attr('placeholder', 'Search with Full Name, Company name Or Number');
            }
        });
    
        // Handle team member selection
        $('.dropdown-item').click(function(e) {
            e.preventDefault();
            var memberId = $(this).data('member-id');
            var memberName = $(this).text();
            updateSelectedMemberName(memberName);
            reloadDataTable(memberId);
        });
    
        $('table').on('draw.dt', function () {
            $('td[data-data="owner_full_name"]').each(function () {
                var text = $(this).text();
                if (text.length > 20) {
                    $(this).text(text.substring(0, 20) + '...'); // Adjusted to show up to 20 characters
                }
            });
        });
    
    });
    </script>
    
@endpush


@endpush
