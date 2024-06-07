@extends('layouts.app')

@section('content')
<div class="row page-titles trust-wave mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>{{ $user->first_name }} {{ $user->last_name }}'s Total Leads</h4>
        </div>
    </div>
   
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5>Lead Data</h5>
                <!-- Search Form -->
                <form action="{{ route('team.leads', ['id' => $user->id]) }}" method="GET">
                    <input type="text" name="search" placeholder="Search here" value="{{ request()->search }}" class="form-control" style="width: 200px; display: inline-block; margin-right: 10px;">
                    <button type="submit" class="btn btn-primary">Search</button>
                </form>
            </div>
            
            <div class="card-body">
                <div class="table-responsive">
                    @if ($leads->isEmpty())
                        <h5>No data available</h5>
                    @else
                        <table class="display " style="width:100%">
                            <thead>
                                <tr>
                                   {{-- <th>Lead ID</th> --}}
                                   <th>Company Name</th>
                                   <th>Lead Full Name</th>
                                   <th>Lead Status</th>
                                   <th>Remark</th>
                                   <th>Visit Date</th>
                                   <th>More Info</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($leads as $lead)
                                <tr>
                                    {{-- <td>{{ $lead->id }}</td> --}}
                                    <td>{{ \Illuminate\Support\Str::limit($lead->business->name ?? 'N/A', 20, $end='...') }}</td>
                                    <td>{{  \Illuminate\Support\Str::limit($lead->business->owner_first_name  ?? 'N/A', 20, $end='...') }} {{   \Illuminate\Support\Str::limit($lead->business->owner_last_name  ?? 'N/A', 20, $end='...') }}</td>
                                    <td style="padding: 10px">{!! $lead->leadStatus($lead->status) !!}</td>
                                    <td>{{ $lead->remark ?? 'N/A'}}</td>
                                    <td>{{ $lead->visit_date }}</td>
                                    <th style="padding-left: 15px ;">    <a href="{{ route('teams.detail', ['id' => $lead]) }}">
                                        <i class="fa fa-eye" style="font-size:20px;"></i></a></th>
                                </tr>
                                <tr class="divider-row">
                                    <td colspan="6"><hr></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
