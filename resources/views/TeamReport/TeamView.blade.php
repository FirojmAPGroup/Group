@extends('layouts.app')

@section('content')
<div class="row page-titles trust-wave mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>User Reports</h4>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 d-flex justify-content-end">
       
        <form action="{{ route('teams.view') }}" method="GET" id="filterForm" class="d-inline" style="font-size: 15px">
            <select id="filterSelect" name="filter" onchange="this.form.submit()"
                class="btn" style="color:black;font-size: 15px">
                @foreach ($Options as $key => $option)
                    <option value="{{ $key }}" {{ $selectedFilter == $key ? 'selected' : '' }}>
                        {{ $option }}</option>
                @endforeach
            </select>
        </form>
        @if ($selectedFilter == 'day_wise')
            <form action="{{ route('teams.view') }}" method="GET">
                <input type="hidden" name="filter" value="day_wise">
                <input type="date" name="selected_date" value="{{ $selectedDate ?? '' }}"class="form-control" style="width: 150px; display: inline-block; margin-right: 10px;margin-left:10px">
                <button type="submit" class="btn trust-wave-button-color">Apply</button>
            </form>
        @elseif ($selectedFilter == 'team_member_wise')
        <form action="{{ route('teams.view') }}" method="GET" class="d-inline" style="padding-left:5px;padding-right:5px;">
            <input type="hidden" name="filter" value="team_member_wise">
            <select id="selected_member" name="selected_member" class="btn " style="color:black;" onchange="this.form.submit()">
                @php $defaultName = "Select User Member"; @endphp
                <option value="">{{ isset($selectedMemberId) ? $defaultName : $defaultName }}</option>
                @foreach ($teamMembers as $member)
                    <option value="{{ $member->id }}" {{ isset($selectedMemberId) && $selectedMemberId == $member->id ? 'selected' : '' }}>
                        {{ $member->first_name }} {{ $member->last_name }}
                    </option>
                @endforeach
            </select>
        </form>
        @elseif ($selectedFilter == 'conversation_wise')
        <form action="{{ route('teams.view') }}" method="GET"class="d-inline" style="padding-left:5px;padding-right:5px;">
            <input type="hidden" name="filter" value="conversation_wise" class="form-control" style="width: 100px; display: inline-block; margin-right: 10px;margin-left:10px;color:black">
            <select name="conversation_type" class="btn " style="color: black;">
                <option value="" >Total</option>
                <option value="pending" {{ isset($selectedConversationType) && $selectedConversationType == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="complete" {{ isset($selectedConversationType) && $selectedConversationType == 'complete' ? 'selected' : '' }}>Completed</option>

            </select>
            <button type="submit" class="btn trust-wave-button-color">Apply</button>
        </form>
         @endif
        
       
        <form action="{{ route('teams.export') }}" method="GET" style="font-size: 15px;padding-left:5px">
            <input type="hidden" name="filter" value="{{ $selectedFilter }}">
            <input type="hidden" name="selected_date" value="{{ $selectedDate ?? '' }}">
            <input type="hidden" name="selected_member" value="{{ $selectedMemberId ?? '' }}">
            <input type="hidden" name="conversation_type" value="{{ $selectedConversationType ?? '' }}">
            <button type="submit" class="btn" style="color: black">Export</button>
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
                                    <td>{{ strlen($lead['business_name']) > 20 ? substr($lead['business_name'], 0, 20) . '...' : $lead['business_name'] }}</td>
                                    <td>{{ strlen($lead['owner_full_name']) > 20 ? substr($lead['owner_full_name'], 0, 20) . '...' : $lead['owner_full_name'] }}</td>
                                    <td>{{ $lead['assigned_to'] }}</td>
                                    <td style="padding: 10px;">{!! $lead['Status'] !!}</td>
                                    <td>{{ $lead['visit_date'] }}</td>
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
@endsection

@push('js')
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script>
        document.getElementById('selected_member').addEventListener('change', function() {
            var selectElement = document.getElementById('selected_member');
            var selectedOption = selectElement.options[selectElement.selectedIndex];
            selectElement.options[0].text = selectedOption.text;
        });
    </script>
@endpush

@push('script')

@endpush
