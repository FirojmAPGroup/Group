
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
                    <table id="{{ $table }}" class="display " style="width:100%">
                        <thead>
                            <tr>
                                <th style="width: 14.28%">Company Name</th>
                                <th style="width: 10.28%">Lead Full Name</th>
                                <th style="width: 10.28%">Assigned To</th>
                                <th style="width: 10.28%;padding-left: 15px ;">Status</th>
                                <th style="width: 8.28%">Visit Date</th>
                                <th style="width: 8.28%;padding-left: 15px ;">Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($leads as $lead)
                           
                            <tr>
                                <td>{{ $lead['business_name'] }}</td>
                                <td >{{ $lead['owner_name'] }}</td>
                                <td>{{ $lead['assigned_to'] }}</td>

                                <td style="padding: 10px ;">{!! $lead['Status'] !!}</td>
                                <td>{{ $lead['visit_date'] }}</td>
                               <th style="padding-left: 15px ;">    <a href="{{ route('teams.detail', ['id' => $lead['id']]) }}">
                                <i class="fa fa-eye" style="font-size:20px;"></i></a></th>
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

@endpush
