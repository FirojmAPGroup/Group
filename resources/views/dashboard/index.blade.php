@extends('layouts.app')

@push('css')
<link href="{{ pathAssets('vendor/chartist/css/chartist.min.css') }}" rel="stylesheet">
<link rel="stylesheet" href="{{ pathAssets('vendor/datatables/css/jquery.dataTables.min.css') }}">
@endpush

@section('content')
<div class="row page-titles trust-wave mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Hi, welcome back!</h4>
            <p class="mb-0">Your business dashboard template</p>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-3 col-sm-6">
        <div class="card"  style="background-color: #ee7c08">
        <a href="{{ routePut('leads.getlead',['status'=>'total']) }}">
            <div class="stat-widget-one card-body">
                <div class="stat-icon d-inline-block">
                    <i class="fa fa-circle" style="color: aliceblue"></i>
                </div>
                <div class="stat-content d-inline-block">
                    <div class="stat-text" >Total Visits</div>
                    <div class="stat-digit" > {{ totalVisit() }}</div>
                </div>
            </div>
        </a>
        </div>
    </div>
    <div class="col-lg-3 col-sm-6">
        <div class="card"  style="background-color: #ee7c08">
            <a href="{{ routePut('leads.getlead',['status'=>'completed']) }}">
            <div class="stat-widget-one card-body">
                <div class="stat-icon d-inline-block">
                    <i class="fa fa-check-circle" style="color: aliceblue"></i>
                </div>
                <div class="stat-content d-inline-block">
                    <div class="stat-text">Completed Visits</div>
                    <div class="stat-digit">{{ completedVisit() }}</div>
                </div>
            </div>
        </a>
        </div>
    </div>
    <div class="col-lg-3 col-sm-6">
        <div class="card"  style="background-color: #ee7c08">
            <a href="{{ routePut('leads.getlead',['status'=>'pending']) }}" class="pending-element">
            <div class="stat-widget-one card-body">
                <div class="stat-icon d-inline-block">
                    <i class="fa fa-circle-o-notch " style="color: aliceblue"></i>
                </div>
                <div class="stat-content d-inline-block">
                    <div class="stat-text">Pending Visits</div>
                    <div class="stat-digit">{{ pendingVisit() }}</div>
                </div>
            </div>
        </a>
        </div>
    </div>
    <div class="col-lg-3 col-sm-6">
        <div class="card"  style="background-color: #ee7c08">
            <a href="{{ routePut('teams.list')}}" class="pending-element">
            <div class="stat-widget-one card-body">
                <div class="stat-icon d-inline-block">
                    <i class="fas fa-id-card-alt  " style="color:aliceblue"></i>
                </div>
                <div class="stat-content d-inline-block">
                    <div class="stat-text">Total Teams</div>
                    <div class="stat-digit">{{ totalTeam() }}</div>
                </div>
            </div>
        </a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Reports</h4>
                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="intervalDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Select Interval
                    </button>
                    <div class="dropdown-menu" aria-labelledby="intervalDropdown">
                        <a class="dropdown-item" href="#" data-interval="today" onclick="fetchChartData('today')">Today</a>
                        <a class="dropdown-item" href="#" data-interval="week" onclick="fetchChartData('week')">This Week</a>
                        <a class="dropdown-item" href="#" data-interval="month" onclick="fetchChartData('month')">This Month</a>
                        <a class="dropdown-item" href="#" data-interval="month" onclick="fetchChartData('all')">All Data</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="ct-bar-chart mt-5"></div>
                <div id="selectedInterval" class="mt-2"></div> <!-- Added element to display selected interval -->
                <div id="chartLegend" class="mt-3"></div> <!-- Added element for chart legend -->
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3>Today's Visit</h3>
            </div>
            <div class="col p-md-0 d-flex justify-content-end" style="margin-top:-40px;margin-left:-10px"> <!-- Adjusted class for left-side positioning -->
                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="teamMemberDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Select Team Member
                    </button>
                    <div class="dropdown-menu" aria-labelledby="teamMemberDropdown">
                        @foreach($teamMembers as $member)
                        <a class="dropdown-item" href="#" data-member-id="{{ $member->id }}">{{ $member->first_name }} {{ $member->last_name }}</a>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="{{ $table }}" class="display" style="width:100%">
                        <thead>
                            <tr>
                                <th data-data="name">Company Name</th>
                                <th data-data="lead_first_name">Lead First Name</th>
                                <th data-data="lead_last_name">Lead Last Name</th>
                                <th data-data="lead_number">Lead Number</th>
                                <th data-data="ti_status">Status</th>
                                <th data-data="distance">Distance</th>
                                <th data-data="first_name">Team First Name</th>
                                <th data-data="last_name">Team Last Name</th>
                                <th data-data="phone_number">Team Number</th>
                                <th data-data="details"><i class="fa fa-eye" style="font-size:20px;"></i>Details</th>
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
<script src="{{ pathAssets('vendor/chartist/js/chartist.min.js') }}"></script>
<script src="{{ pathAssets('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
@endpush

@push('script')
<script>
    jQuery(document).ready(function() {
        var dtTable;

        // Initialize DataTable
        dtTable = $('#{{ $table }}').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": '{{ route("app.todayVisits") }}',
                "type": "GET",
                "data": { member_id: '' } // Initial data fetch with no specific member
            },
            "paging": false, // Enable pagination
            "lengthChange": true, // Enable length change
            "searching": false, // Enable search
            "ordering": false, // Enable ordering
            "info": true, // Enable info
            "autoWidth": true, // Disable auto width calculation
            "responsive": true // Enable responsiveness
        });

        // Function to reload DataTable with selected team member
        function reloadDataTable(memberId) {
            dtTable.ajax.url('{{ route("app.todayVisits") }}' + '?member_id=' + memberId).load();
        }

        // Function to update selected member name in the dropdown button
        function updateSelectedMemberName(memberName) {
            $('#teamMemberDropdown').html(memberName);
        }

        // Handle team member selection
        $('.dropdown-item').click(function(e) {
            e.preventDefault();
            var memberId = $(this).data('member-id');
            var memberName = $(this).text();
            updateSelectedMemberName(memberName);
            reloadDataTable(memberId);
        });
    });
    function fetchChartData(interval) {
        $.ajax({
            url: "{{ route('app.dashboard') }}",
            type: 'GET',
            data: { interval: interval },
            success: function(response) {
                updateChart(response);
                updateSelectedInterval(interval);
            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });
    }

    function updateChart(data) {
        var barChart = {
            labels: data.labels, // Use dynamic labels from the response
            series: [
                data.totalVisits,
                data.completedVisits,
                data.pendingVisits,
                data.unassignedVisits
            ]
        };

        var barOptions = {
            seriesBarDistance: 10
        };

        var barResponsiveOptions = [
            ['screen and (max-width: 640px)', {
                seriesBarDistance: 5,
                axisX: {
                    labelInterpolationFnc: function(value) {
                        return value.split(' ')[0]; // Show only the date for small screens
                    }
                }
            }]
        ];

        new Chartist.Bar('.ct-bar-chart', barChart, barOptions, barResponsiveOptions);

        // Update legend
        var legend = document.getElementById('chartLegend');
        legend.innerHTML = '';
        var legendItems = ['Total Visits', 'Completed Visits', 'Pending Visits', 'Unassigned Visits'];
        var colors = ['#3366CC', '#DC3912', '#FF9900', '#109618']; // You can set your desired colors here
        legendItems.forEach(function(item, index) {
            var div = document.createElement('div');
            div.innerHTML = `<span class="legend-marker" style="background-color: ${colors[index]}"></span> <span class="legend-text">${item}</span>`;
            legend.appendChild(div);
        });
    }

    function updateSelectedInterval(interval) {
        var intervalText = '';
        switch (interval) {
            case 'today':
                intervalText = 'Today';
                break;
            case 'week':
                intervalText = 'This Week';
                break;
            case 'month':
                intervalText = 'This Month';
                break;
            case 'all':
                intervalText = 'All Data';
                break;
        }
        document.getElementById('selectedInterval').innerText = `Selected Interval: ${intervalText}`;
    }

    $(document).ready(function() {
        fetchChartData('today'); // Initial load
    });
</script>
@endpush
