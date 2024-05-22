{{-- @extends('layouts.app')
@push('css')
    <link rel="stylesheet" href="{{ pathAssets('vendor/datatables/css/jquery.dataTables.min.css') }}">
@endpush

@section('content')
    <div class="row page-titles trust-wave mx-0">
        <div class="col-sm-6 p-md-0">
            <div class="welcome-text ">
                <h4> Team Report </h4>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 d-flex justify-content-end">

            <form action="{{ route('teams.view') }}" method="GET" id="filterForm">
                <select id="filterSelect" name="filter" onchange="this.form.submit()"
                    class="btn trust-wave-button-color btn-rounded btn-warning">
                    @foreach ($Options as $key => $option)
                        <option value="{{ $key }}" {{ $selectedFilter == $key ? 'selected' : '' }}>
                            {{ $option }}</option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="display table-striped" style="width:100%">
                            <thead>
                                <tr>
                                    <th data-data="business_name">Business Name</th>
                                    <th data-data="owner_name">Owner Name</th>
                                    <th data-data="Status">Status</th>
                                    <th data-data="assigned_to">Lead To</th>
                                    <th data-data="assigned_email">Assigned Email</th>
                                    <th data-data="created_at">Created at</th>
                                    <th data-data="assigned_on">Assigned On</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($leads as $lead)
                                <tr>
                                    <td>{{ $lead['business_name'] }}</td>
                                    <td>{{ $lead['owner_name'] }}</td>
                                    <td>{{ $lead['Status'] }}</td>
                                    <td>{{ $lead['assigned_to'] }}</td>
                                    <td>{{ $lead['assigned_email'] }}</td>
                                    <td>{{ $lead['created_at'] }}</td>
                                    <td>{{ $lead['assigned_on'] }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection --}}


@extends('layouts.app')

@section('content')
<div class="row page-titles trust-wave mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Team Reports</h4>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 d-flex justify-content-end">
        <form action="{{ route('teams.view') }}" method="GET" id="filterForm">
            <select id="filterSelect" name="filter" onchange="this.form.submit()"
                class="btn trust-wave-button-color btn-rounded   has-arrow">
                @foreach ($Options as $key => $option)
                    <option value="{{ $key }}" {{ $selectedFilter == $key ? 'selected' : '' }}>
                        {{ $option }}</option>
                @endforeach
            </select>
        </form>
        
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                   {{-- <div class="row ">
                    <div class="dataTables_length col-sm-6 p-md-0" id="tableTeams_length" style="padding-left: 15px !important">
                        <label>Show 
                            <select name="tableTeams_length" aria-controls="tableTeams" class="">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="75">75</option>
                            <option value="100">100</option>
                        </select> 
                        entries</label>
                    </div>
                    <div id="tableLeads_filter" 
                    class="dataTables_filter col-sm-6 p-md-0 d-flex justify-content-end " style="padding-right: 15px !important">
                         <label>Search:<input type="search" class="" placeholder="" aria-controls="tableLeads">
                    </label>
                        </div>
                   </div> --}}

                    <table id="{{ $table }}" class="display table-striped" style="width:100%">
                        <thead>
                            <tr>
                                <th style="width: 14.28%">Business Name</th>
                                <th style="width: 20.28%">Owner Name</th>
                                <th style="width: 10.28%;padding-left: 15px ;">Status</th>
                                <th style="width: 14.28%">Assigned To</th>
                                <th style="width: 14.28%">Assigned Email</th>
                                <th style="width: 10.28%">Created At</th>
                                <th style="width: 10.28%">Assigned On</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($leads as $lead)
                           
                            <tr>
                                <td>{{ $lead['business_name'] }}</td>
                                <td>{{ $lead['owner_name'] }}</td>
                                <td style="padding-left: 15px ">{{ $lead['Status'] }}</td>
                                <td>{{ $lead['assigned_to'] }}</td>
                                <td>{{ $lead['assigned_email'] }}</td>
                                <td>{{ $lead['created_at'] }}</td>
                                <td>{{ $lead['assigned_on'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
@endpush

@push('script')
<script>
    jQuery(document).ready(function() {
        var dtTable = $('#{{ $table }}').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("teams.load") }}',
                data: function (d) {
                    d.filter = "{{ $selectedFilter }}";
                }
            },
            columns: [
                { data: 'business_name', name: 'business_name' },
                { data: 'owner_name', name: 'owner_name' },
                { data: 'Status', name: 'Status' },
                { data: 'assigned_to', name: 'assigned_to' },
                { data: 'assigned_email', name: 'assigned_email' },
                { data: 'created_at', name: 'created_at' },
                { data: 'assigned_on', name: 'assigned_on' }
            ]
        });
        $('#filterSelect').on('change', function() {
            var filter = $(this).val();
            dtTable.ajax.url('{{ route("teams.load") }}?/' + filter).load();
        });
        
        // $('#tableLeads_filter input[type="search"]').on('keyup', function () {
        //     dtTable.search(this.value).draw();
        // });

        // $('#filterSelect').on('change', function() {
        //     var filter = $(this).val();
        //     dtTable.ajax.url('{{ route("teams.load") }}?/' + filter).load();
        // });
    });
</script>
@endpush
