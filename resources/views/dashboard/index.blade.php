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
    @php
        $cards = [
            ['route' => routePut('leads.getlead', ['status' => 'total']), 'icon' => 'fa-circle', 'text' => 'Total Visits', 'value' => totalVisit()],
            ['route' => routePut('leads.getlead', ['status' => 'completed']), 'icon' => 'fa-check-circle', 'text' => 'Completed Visits', 'value' => completedVisit()],
            ['route' => routePut('leads.getlead', ['status' => 'pending']), 'icon' => 'fa-circle-o-notch', 'text' => 'Pending Visits', 'value' => pendingVisit()],
            ['route' => routePut('teams.list'), 'icon' => 'fas fa-id-card-alt', 'text' => 'Total Teams', 'value' => totalTeam()]
        ];
    @endphp

    @foreach ($cards as $card)
        <div class="col-lg-3 col-sm-6">
            <div class="card" style="background-color: #ee7c08">
                <a href="{{ $card['route'] }}">
                    <div class="stat-widget-one card-body">
                        <div class="stat-icon d-inline-block">
                            <i class="fa {{ $card['icon'] }}" style="color: aliceblue"></i>
                        </div>
                        <div class="stat-content d-inline-block">
                            <div class="stat-text">{{ $card['text'] }}</div>
                            <div class="stat-digit">{{ $card['value'] }}</div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    @endforeach
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
                        @foreach([ 'week' => 'This Week', 'month' => 'This Month', 'all' => 'All Data'] as $key => $value)
                            <a class="dropdown-item" href="#" data-interval="{{ $key }}" onclick="fetchChartData('{{ $key }}')">{{ $value }}</a>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="ct-bar-chart mt-5"></div>
                <div id="selectedInterval" class="mt-2"></div>
                <div id="chartLegend" class="mt-3"></div>
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
            <div class="col p-md-0 d-flex justify-content-end" style="margin-top:-40px; margin-left:-10px">
                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="teamMemberDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Select Team Member
                    </button>
                    <div class="dropdown-menu" aria-labelledby="teamMemberDropdown" style="max-height: 200px; overflow-y: auto;">
                        <a class="dropdown-item team-member" href="#" data-member-id="all">Show All</a>
                        @foreach($teamMembers as $member)
                            <a class="dropdown-item team-member" href="#" data-member-id="{{ $member->id }}">{{ $member->first_name }} {{ $member->last_name }}</a>
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
                                <th data-data="lead_first_name">First Name</th>
                                <th data-data="lead_last_name">Last Name</th>
                                <th data-data="lead_number">Number</th>
                                <th data-data="ti_status">Status</th>
                                <th data-data="distance">Distance</th>
                                <th data-data="first_name">First Name</th>
                                <th data-data="last_name">Last Name</th>
                                <th data-data="phone_number">Number</th>
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
{{-- <script>
    $(document).ready(function() {
        var dtTable;

        dtTable = $('#{{ $table }}').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("app.todayVisits") }}',
                type: "GET",
                data: { member_id: '' }
            },
            paging: true,
            lengthChange: true,
            searching: true,
            ordering: true,
            info: true,
            autoWidth: true,
            responsive: true
        });

        function reloadDataTable(memberId) {
            dtTable.ajax.url('{{ route("app.todayVisits") }}' + '?member_id=' + memberId).load();
        }

        function updateSelectedMemberName(memberName) {
            $('#teamMemberDropdown').html(memberName);
        }

        $('.dropdown-item.team-member').click(function(e) {
            e.preventDefault();
            var memberId = $(this).data('member-id');
            var memberName = $(this).text();
            updateSelectedMemberName(memberName);
            reloadDataTable(memberId);
        });

        fetchChartData('today');
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
            labels: data.labels,
            series: [
                data.totalVisits,
                data.completedVisits,
                data.pendingVisits,
                data.unassignedVisits
            ]
        };

        var barOptions = {
            seriesBarDistance: 10,
            axisY: {
                onlyInteger: true,
                offset: 20,
                labelInterpolationFnc: function(value) {
                    return value;
                }
            }
        };

        var barResponsiveOptions = [
            ['screen and (max-width: 640px)', {
                seriesBarDistance: 5,
                axisX: {
                    labelInterpolationFnc: function(value) {
                        return value.split(' ')[0];
                    }
                }
            }]
        ];

        new Chartist.Bar('.ct-bar-chart', barChart, barOptions, barResponsiveOptions);

        updateLegend();
    }

    function updateLegend() {
        var legend = document.getElementById('chartLegend');
        legend.innerHTML = '';

        var legendItems = [
            { name: 'Total Visits', color: '#3b5998' },
            { name: 'Completed Visits', color: '#1da1f2' },
            { name: 'Pending Visits', color: '#ff0000' },
            { name: 'Unassigned Leads', color: '#d17905' }
        ];

        legendItems.forEach(function(item) {
            var div = document.createElement('div');
            div.innerHTML = `
                <span class="legend-marker" style="background-color: ${item.color}; display: inline-block; width: 12px; height: 12px; margin-right: 5px;"></span>
                <span class="legend-text">${item.name}</span>
            `;
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
</script> --}}

@push('script')
<script>
    $(document).ready(function() {
        var dtTable;

        dtTable = $('#{{ $table }}').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("app.todayVisits") }}',
                type: "GET",
                data: { member_id: '' }
            },
            paging: true,
            lengthChange: true,
            searching: true,
            ordering: true,
            info: true,
            autoWidth: true,
            responsive: true
        });

        function reloadDataTable(memberId) {
            dtTable.ajax.url('{{ route("app.todayVisits") }}' + '?member_id=' + memberId).load();
        }

        function updateSelectedMemberName(memberName) {
            $('#teamMemberDropdown').html(memberName);
        }

        $('.dropdown-item.team-member').click(function(e) {
            e.preventDefault();
            var memberId = $(this).data('member-id');
            var memberName = $(this).text();
            updateSelectedMemberName(memberName);
            reloadDataTable(memberId);
        });

        fetchChartData('week'); // Changed from 'today' to 'week'
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
            labels: data.labels,
            series: [
                data.totalVisits,
                data.completedVisits,
                data.pendingVisits,
                data.unassignedVisits
            ]
        };

        var barOptions = {
            seriesBarDistance: 10,
            axisY: {
                onlyInteger: true,
                offset: 20,
                labelInterpolationFnc: function(value) {
                    return value;
                }
            }
        };

        var barResponsiveOptions = [
            ['screen and (max-width: 640px)', {
                seriesBarDistance: 5,
                axisX: {
                    labelInterpolationFnc: function(value) {
                        return value.split(' ')[0];
                    }
                }
            }]
        ];

        new Chartist.Bar('.ct-bar-chart', barChart, barOptions, barResponsiveOptions);

        updateLegend();
    }

    function updateLegend() {
        var legend = document.getElementById('chartLegend');
        legend.innerHTML = '';

        var legendItems = [
            { name: 'Total Visits', color: '#3b5998' },
            { name: 'Completed Visits', color: '#1da1f2' },
            { name: 'Pending Visits', color: '#ff0000' },
            { name: 'Unassigned Leads', color: '#d17905' }
        ];

        legendItems.forEach(function(item) {
            var div = document.createElement('div');
            div.innerHTML = `
                <span class="legend-marker" style="background-color: ${item.color}; display: inline-block; width: 12px; height: 12px; margin-right: 5px;"></span>
                <span class="legend-text">${item.name}</span>
            `;
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
</script>
@endpush



@endpush
