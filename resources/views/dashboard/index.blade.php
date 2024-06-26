@extends('layouts.app')

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
            ['route' => routePut('teams.list'), 'icon' => 'fas fa-id-card-alt', 'text' => 'Total User', 'value' => totalTeam()]
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

<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header" style="color: black">Reports</div>
            <div class="card-body">
                <form id="filterForm">
                    <div class="form-group row justify-content-end" style="margin: -50px -70px 10px 0px !important">
                        <div class="col-md-2">
                            <select id="member_id" class="form-control" name="member_id">
                                <option value="all">All User Members</option>
                                @foreach ($teamMembers as $member)
                                <option value="{{ $member->id }}" style="color:black">{{ $member->first_name }} {{ $member->last_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select id="interval" class="form-control" name="interval">
                                <option value="today">Today</option>
                                <option value="week">This Week</option>
                                <option value="month">This Month</option>
                                <option value="all">All Data</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary">Apply</button>
                        </div>
                        <div class="col-md-2">
                            <button type="button" id="exportButton" class="btn btn-secondary">Export to Excel</button>
                        </div>
                    </div>
                </form>
                <div class="chart-container" style="width: auto; height: 400px;">
                    <canvas id="myChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- <div class="row">
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
                        <a class="dropdown-item team-member" href="#" data-member-id="all" data-member-name="Show All">Show All</a>
                        @foreach($teamMembers as $member)
                        <a class="dropdown-item team-member" href="#" data-member-id="{{ $member->id }}" data-member-name="{{ $member->first_name }} {{ $member->last_name }}">{{ $member->first_name }} {{ $member->last_name }}</a>
                        @endforeach
                    </div>
                </div>
                <form id="exportForm" method="GET" action="{{ route('export.leads') }}" class="d-inline" style="padding-left:5px">
                    <input type="hidden" name="status" value="{{ request('status') }}">
                    <input type="hidden" name="member_id" id="exportMemberId" value="">
                    <button type="submit" class="btn btn-secondary">Export to Excel</button>
                </form>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="{{ $table }}" class="display" style="width:100%">
                        <thead>
                            <tr>
                                <th style="width: 14.28%">Company Name</th>
                                <th style="width: 14.28%">Lead Full Name</th>
                                <th style="width: 14.28%">Lead Number</th>
                                <th style="width: 10.28%">Status</th>
                                <th style="width: 8.28%">Distance</th>
                                <th style="width: 14.28%">User Full Name</th>
                                <th style="width: 14.28%">User Phone Number</th>
                                <th style="width: 10.28%">Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($leads as $lead)
                            <tr>
                                <td>{{ \Illuminate\Support\Str::limit($lead->business->name, 20, $end='...') }}</td>
                                <td>{{ \Illuminate\Support\Str::limit($lead->business->owner_full_name, 20, $end='...') }}</td>
                                <td>{{ $lead->business->owner_number }}</td>
                                <td style="padding: 10px;">{!! $lead->leadStatus() !!}</td>
                                <td>{{ $lead->distance }}</td>
                                <td>{{ $lead->user->first_name }} {{ $lead->user->last_name }} </td>
                                <td>{{ $lead->user->phone_number }}</td>
                                <td style="padding-left: 15px;">
                                    <a href="{{ route('teams.detail', ['id' => $lead['id']]) }}">
                                        <i class="fa fa-eye" style="font-size:20px;"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div> --}}
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
                        <a class="dropdown-item team-member" href="#" data-member-id="all" data-member-name="Show All">Show All</a>
                        @foreach($teamMembers as $member)
                        <a class="dropdown-item team-member" href="#" data-member-id="{{ $member->id }}" data-member-name="{{ $member->first_name }} {{ $member->last_name }}">{{ $member->first_name }} {{ $member->last_name }}</a>
                        @endforeach
                    </div>
                </div>
                <form id="exportForm" method="GET" action="{{ route('export.leads') }}" class="d-inline" style="padding-left:5px">
                    <input type="hidden" name="status" value="{{ request('status') }}">
                    <input type="hidden" name="member_id" id="exportMemberId" value="">
                    <button type="submit" class="btn btn-secondary">Export to Excel</button>
                </form>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="{{ $table }}" class="display" style="width:100%">
                        <thead>
                            <tr>
                                <th style="width: 14.28%">Company Name</th>
                                <th style="width: 14.28%">Lead Full Name</th>
                                <th style="width: 14.28%">Lead Number</th>
                                <th style="width: 10.28%">Status</th>
                                <th style="width: 8.28%">Distance</th>
                                <th style="width: 14.28%">User Full Name</th>
                                <th style="width: 14.28%">User Phone Number</th>
                                <th style="width: 10.28%">Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($leads as $lead)
                            <tr>
                                <td>{{ \Illuminate\Support\Str::limit($lead->business->name, 20, $end='...') }}</td>
                                <td>{{ \Illuminate\Support\Str::limit($lead->business->owner_full_name, 20, $end='...') }}</td>
                                <td>{{ $lead->business->owner_number }}</td>
                                <td style="padding: 10px;">{!! $lead->leadStatus() !!}</td>
                                <td>{{ $lead->distance }}</td>
                                <td>{{ $lead->user->first_name }} {{ $lead->user->last_name }} </td>
                                <td>{{ $lead->user->phone_number }}</td>
                                <td style="padding-left: 15px;">
                                    <a href="{{ route('teams.detail', ['id' => $lead['id']]) }}">
                                        <i class="fa fa-eye" style="font-size:20px;"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('js')
<script src="{{ pathAssets('vendor/chartist/js/chartist.min.js') }}"></script>
<script src="{{ pathAssets('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  $(document).ready(function() {
    function updateTodayVisitTable(memberId) {
        $.ajax({
            url: '{{ route('app.todayVisits') }}',
            method: 'GET',
            data: { member_id: memberId },
            success: function(response) {
                $('#{{ $table }} tbody').empty();
                $.each(response.leads, function(index, lead) {
                    var row = '<tr>' +
                        '<td>' + lead.business_name + '</td>' +
                        '<td>' + lead.owner_full_name + '</td>' +
                        '<td>' + lead.owner_number + '</td>' +
                        '<td>' + lead.ti_status + '</td>' +
                        '<td>' + lead.distance + '</td>' +
                        '<td>' + lead.user_full_name + '</td>' +
                        '<td>' + lead.user_phone_number + '</td>' +
                        '<td><a href="' + lead.details_link + '"><i class="fa fa-eye" style="font-size:20px;"></i></a></td>' +
                        '</tr>';
                    $('#{{ $table }} tbody').append(row);
                });
            },
            error: function(xhr, status, error) {
                console.error('Error fetching data:', error);
            }
        });
    }

    $('#teamMemberDropdown').on('click', '.team-member', function(e) {
        e.preventDefault();
        var memberId = $(this).data('member-id');
        var memberName = $(this).data('member-name');
        $('#teamMemberDropdown').text(memberName);
        updateTodayVisitTable(memberId);
    });
});

</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var ctx = document.getElementById('myChart').getContext('2d');

        var initialData = {
            labels: [],
            datasets: [{
                    label: 'Total Visits',
                    backgroundColor: '#6610f2',
                    borderColor: '#6610f2',
                    borderWidth: 1,
                    data: []
                },
                {
                    label: 'Completed Visits',
                    backgroundColor: '#7ED321',
                    borderColor: '#7ED321',
                    borderWidth: 1,
                    data: []
                },
                {
                    label: 'Pending Visits',
                    backgroundColor: '#EE3232',
                    borderColor: '#EE3232',
                    borderWidth: 1,
                    data: []
                }
            ]
        };

        var myChart = new Chart(ctx, {
            type: 'bar',
            data: initialData,
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                },
                plugins: {
                    legend: {
                        labels: {
                            usePointStyle: true,
                        }
                    }
                },
                maintainAspectRatio: false
            }
        });

        function updateChartData(memberId, interval) {
            fetch("{{ route('dashboard.data') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    member_id: memberId,
                    interval: interval
                })
            })
            .then(response => response.json())
            .then(data => {
                myChart.data.labels = data.labels;
                myChart.data.datasets[0].data = data.totalVisits;
                myChart.data.datasets[1].data = data.completedVisits;
                myChart.data.datasets[2].data = data.pendingVisits;
                myChart.update();
            })
            .catch(error => console.error('Error:', error));
        }

        updateChartData('all', 'week');

        document.getElementById('filterForm').addEventListener('submit', function(event) {
            event.preventDefault();
            var memberId = document.getElementById('member_id').value;
            var interval = document.getElementById('interval').value;
            updateChartData(memberId, interval);
        });

        document.getElementById('exportButton').addEventListener('click', function() {
            var memberId = document.getElementById('member_id').value;
            var interval = document.getElementById('interval').value;
            var exportUrl = '{{ route("export.leads") }}' + '?member_id=' + memberId + '&interval=' + interval;
            window.location.href = exportUrl;
        });
    });
</script>
@endpush

@endsection
