@extends('layouts.app')

@push('css')
<link rel="stylesheet" href="{{ pathAssets('vendor/datatables/css/jquery.dataTables.min.css') }}">
@endpush

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-0">
        <div class="welcome-text">
            <h4>{{ $heading }} Sub Admin</h4>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Form Validation</h4>
            </div>
            <div class="card-body">
                <form id="frmSubAdmin" class="form-valide cls-crud-simple-save" action="{{ route('subadmin.save') }}" method="post">
                    @csrf
                    <input type="hidden" name="id" value="{{ $user ? encId($user->getId()) : encId(0) }}">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="first_name" style="color: black">First Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="first_name" name="first_name" placeholder="First Name" value="{{ $user->first_name }}">
                            </div>
                            <div class="form-group">
                                <label for="email" style="color: black">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="Your valid email.." required value="{{ $user->email }}">
                            </div>
                            @if(!$user->getId())
                            <div class="form-group">
                                <label for="password" style="color: black">Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                            </div>
                            @endif
                            <div class="form-group">
                                <label for="phone" style="color: black">Phone Number <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="phone" name="phone" placeholder="Phone Number" value="{{ $user->phone_number }}" required maxlength="10">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="last_name" style="color: black">Last Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Last Name" value="{{ $user->last_name }}" required>
                            </div>
                            <div class="form-group">
                                <label for="admin_title" style="color: black">Admin Title <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="admin_title" name="admin_title" required>
                                    <option value="">Please select</option>
                                    {!! makeDropdown(siteConfig('title'), $user->title) !!}
                                </select>
                            </div>
                            @if(!$user->getId())
                            <div class="form-group">
                                <label for="password_confirmation" style="color: black">Confirm Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Confirm Password" required>
                            </div>
                            @endif
                        </div>
                    </div>
                   
                    <div class="form-group">
                        <label style="color: black">Permissions <span class="text-danger">*</span></label>
                        <div style="display:flex;flex-direction:row;justify-content:space-between;">
                            <div>
                                <input type="checkbox" id="selectAll"> <label class="form-check-label">Select All Permissions</label>
                            </div>
                        </div>
                        @foreach($permissions as $group => $groupPermissions)
                            <div class="mt-3" style="margin-left:10px">
                                <h5>{{ ucfirst($group) }} Permissions</h5>
                                <div class="row">
                                    @foreach($groupPermissions as $permission)
                                        <div class="col-lg-3 col-md-4 col-sm-6">
                                            <div class="form-check">
                                                <input class="form-check-input checkbox" type="checkbox" name="permission[]" value="{{ $permission }}" {{ in_array($permission, $selectedPermissions) ? 'checked' : '' }}>
                                                <label class="form-check-label">{{ $permission }}</label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="form-group text-right">
                        <button type="submit" class="btn trust-wave-button-color">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@stop

@push('js')
<script src="{{ pathAssets('vendor/jquery-validation/jquery.validate.min.js') }}"></script>
@endpush

@push('script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Capitalize first letter functionality
        var inputs = document.querySelectorAll('input[type="text"], textarea');
        inputs.forEach(function(input) {
            input.addEventListener('input', function() {
                this.value = this.value.charAt(0).toUpperCase() + this.value.slice(1);
            });
        });

        // Select all checkboxes functionality
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.checkbox');

        selectAll.addEventListener('change', function() {
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                if (!this.checked) {
                    selectAll.checked = false;
                } else {
                    const allChecked = Array.from(checkboxes).every(chk => chk.checked);
                    selectAll.checked = allChecked;
                }
            });
        });

    });
</script>
@endpush
