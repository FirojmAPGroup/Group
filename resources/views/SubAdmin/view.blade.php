@extends('layouts.app')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-0">
        <div class="welcome-text">
            <h4>SubAdmin Details</h4>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Sub-Admin Details :</h5>
                        <div class="mb-3" style="color: black">
                            <strong style="color: black;padding-right:10px">Name:</strong> {{ $subAdmin->first_name }} {{ $subAdmin->last_name }}
                        </div>
                        <div class="mb-3" style="color: black">
                            <strong style="color: black;padding-right:10px"  >Email:</strong> {{ $subAdmin->email }}
                        </div>
                        <div class="mb-3" style="color: black">
                            <strong style="color: black;padding-right:10px">Phone Number:</strong> {{ $subAdmin->phone_number }}
                        </div>
                        <div class="mb-3" style="color: black">
                            <strong style="color: black;padding-right:10px">Roles:</strong>
                            @foreach($subAdmin->roles as $role)
                                {{ $role->name }}<br>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h5>Permissions :</h5>
                        <ul>
                            @foreach($subAdmin->getAllPermissions() as $permission)
                                <li style="color: black;font-size:15px;padding-right:10px">{{ $permission->name }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
