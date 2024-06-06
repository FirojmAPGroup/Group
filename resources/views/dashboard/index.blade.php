{{-- @extends('layouts.app')
@push('css')
<link href="{{ pathAssets('vendor/chartist/css/chartist.min.css') }}" rel="stylesheet">
<link rel="stylesheet" href="{{ pathAssets('vendor/datatables/css/jquery.dataTables.min.css') }}">
@endpush
@push('style')
    <style>
        .pending-element {
            color: #BDBDC7 !important;
        }

        /* Apply styles on hover */
        .pending-element:hover {
             color: #BDBDC7 !important;
        }
    </style>
@endpush
@section('content')
<div class="row page-titles trust-wave mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text ">
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
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Reports</h4>
            </div>
            <div class="card-body">
                <div class="ct-bar-chart mt-5"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <div class="ct-pie-chart"></div>
            </div>
            <div class="legend-box ">
                <div class="legend-item">
                    <span class="legend-color bg-facebook"></span>
                    <span>Total Visit</span>
                </div>
                <div class="legend-item">
                    <span class="legend-color bg-success"></span>
                    <span>Completed Visit</span>
                </div>
                <div class="legend-item">
                    <span class="legend-color bg-google-plus "></span>
                    <span>Pending Visit</span>
                </div>
                <div class="legend-item">
                    <span class="legend-color bg-twitter"></span>
                    <span>Not Assign</span>
                </div>
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

                                <th data-data="first_name"> Team First Name</th>
                                <th data-data="last_name">Team Last Name</th>
                                <th data-data="phone_number">Team Number</th>
                               
                                <th data-data="details">  <i class="fa fa-eye" style="font-size:20px;"></i>Details</th>
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
        //  pie chart
         var chartData = {!! pieChart() !!};
         var options = {
            labelInterpolationFnc: function(value) {
                return value[0]
            },
            showLabel:false,
            chartPadding: 30,
            labelOffset: 50,
            labelDirection: 'explode'
        };

        var responsiveOptions = [
            ['screen and (min-width: 640px)', {
                chartPadding: 30,
                labelOffset: 100,
                labelDirection: 'explode',
                labelInterpolationFnc: function(value) {
                    return value;
                }
            }],
            ['screen and (min-width: 1024px)', {
                labelOffset: 80,
                chartPadding: 20
            }]
        ];

        new Chartist.Pie('.ct-pie-chart', chartData, options, responsiveOptions);

        // bar chart
        var barChart = {!! barChart() !!}
        var options = {
        seriesBarDistance: 10
    };

    var responsiveOptions = [
        ['screen and (max-width: 640px)', {
            seriesBarDistance: 5,
            axisX: {
                labelInterpolationFnc: function(value) {
                    return value[0];
                }
            }
        }]
    ]; --}}
    @extends('layouts.app')
    @push('css')
    <link href="{{ pathAssets('vendor/chartist/css/chartist.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ pathAssets('vendor/datatables/css/jquery.dataTables.min.css') }}">
    @endpush
    @push('style')
    <style>
        .pending-element {
            color: #BDBDC7 !important;
        }
    
        .pending-element:hover {
            color: #BDBDC7 !important;
        }
    </style>
    @endpush
    @section('content')
    <div class="row page-titles trust-wave mx-0">
        <div class="col-sm-6 p-md-0">
            <div class="welcome-text ">
                <h4>Hi, welcome back!</h4>
                <p class="mb-0">Your business dashboard template</p>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-lg-3 col-sm-6">
            <div class="card" style="background-color: #ee7c08">
                <a href="{{ routePut('leads.getlead',['status'=>'total']) }}">
                    <div class="stat-widget-one card-body">
                        <div class="stat-icon d-inline-block">
                            <i class="fa fa-circle" style="color: aliceblue"></i>
                        </div>
                        <div class="stat-content d-inline-block">
                            <div class="stat-text">Total Visits</div>
                            <div class="stat-digit">{{ $totalVisits }}</div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="card" style="background-color: #ee7c08">
                <a href="{{ routePut('leads.getlead',['status'=>'completed']) }}">
                    <div class="stat-widget-one card-body">
                        <div class="stat-icon d-inline-block">
                            <i class="fa fa-check-circle" style="color: aliceblue"></i>
                        </div>
                        <div class="stat-content d-inline-block">
                            <div class="stat-text">Completed Visits</div>
                            <div class="stat-digit">{{ $completedVisits }}</div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="card" style="background-color: #ee7c08">
                <a href="{{ routePut('leads.getlead',['status'=>'pending']) }}" class="pending-element">
                    <div class="stat-widget-one card-body">
                        <div class="stat-icon d-inline-block">
                            <i class="fa fa-circle-o-notch" style="color: aliceblue"></i>
                        </div>
                        <div class="stat-content d-inline-block">
                            <div class="stat-text">Pending Visits</div>
                            <div class="stat-digit">{{ $pendingVisits }}</div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="card" style="background-color: #ee7c08">
                <a href="{{ routePut('teams.list')}}" class="pending-element">
                    <div class="stat-widget-one card-body">
                        <div class="stat-icon d-inline-block">
                            <i class="fas fa-id-card-alt" style="color:aliceblue"></i>
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
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Reports</h4>
                </div>
                <div class="card-body">
                    <div class="ct-bar-chart mt-5"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="ct-pie-chart"></div>
                </div>
                <div class="legend-box">
                    <div class="legend-item">
                        <span class="legend-color bg-facebook"></span>
                        <span>Total Visit</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-color bg-twitter"></span>
                        <span>Completed Visit</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-color bg-google-plus"></span>
                        <span>Pending Visit</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-color bg-linkedin" ></span>
                        <span>Not Assign</span>
                    </div>
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
        // Pie chart data
        var chartData = {
            labels: ['Total Visit', 'Completed Visit', 'Pending Visit', 'Not Assign'],
            series: [{{ $totalVisits }}, {{ $completedVisits }}, {{ $pendingVisits }}, {{ $unassignedVisits }}]
        };
  

        var options = {
            labelInterpolationFnc: function(value) {
                return value[0]
            },
            showLabel: false,
            chartPadding: 30,
            labelOffset: 50,
            labelDirection: 'explode',
            
        };
    
        var responsiveOptions = [
            ['screen and (min-width: 640px)', {
                chartPadding: 30,
                labelOffset: 100,
                labelDirection: 'explode',
                labelInterpolationFnc: function(value) {
                    return value;
                }
            }],
            ['screen and (min-width: 1024px)', {
                labelOffset: 80,
                chartPadding: 20
            }]
        ];
    
        new Chartist.Pie('.ct-pie-chart', chartData, options, responsiveOptions);
    
        // Bar chart data
        var barChart = {
            labels: ['Total Visit', 'Completed Visit', 'Pending Visit', 'Not Assign'],
            series: [
                [{{ $totalVisits }}, {{ $completedVisits }}, {{ $pendingVisits }}, {{ $unassignedVisits }}]
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
                        return value[0];
                    }
                }
            }]
        ];
    
        new Chartist.Bar('.ct-bar-chart', barChart, barOptions, barResponsiveOptions);
    
    
    // new Chartist.Bar('.ct-bar-chart', barChart, options, responsiveOptions);

    jQuery(document).ready(function() {
      dtTable = applyDataTable('#{{$table}}', '{!! $urlListData ?? "" !!}', {
        "paging": false, // Enable pagination
        "lengthChange": true, // Enable length change
        "searching": false, // Enable search
        "ordering": false, // Enable ordering
        "info": true, // Enable info
        "autoWidth": true, // Disable auto width calculation
        "responsive": true // Enable responsiveness
      });
    });
    </script>
@endpush
