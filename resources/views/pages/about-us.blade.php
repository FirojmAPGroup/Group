@extends('layouts.app')
@push('css')
    <link href="{{ pathAssets('vendor/summernote/summernote.css') }}" rel="stylesheet">
@endpush
@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>{{ $title }} </h4>
        </div>
    </div>
</div>
<!-- row -->
<div class="row">
    <div class="col-xl-12 col-xxl-12">
        <div class="card">
            <div class="card-body">
                <form id="frmSubAdmin" class="form-valide cls-crud-simple-save" action="{{ routePut('pages.about-us') }}" method="post">
                    <div class="form-group row">
                        <label class="col-md-2 col-form-label" for="aboutus">Content
                            <span class="text-danger">*</span>
                        </label>
                        <div class="col-md-10">
                            <textarea name="aboutus" id="aboutus" class="summernote">{!! $page->content !!}</textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col md-auto">
                            <a href="{{ route('app.dashboard') }}" class="btn btn-secondary  ">Back</a>
                        </div>
                        <div class="col-lg-8 ml-auto text-right">
                            <button type="submit" class="btn trust-wave-button-color">Update</button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@stop
@push('js')
    <script src="{{ pathAssets('vendor/jquery-validation/jquery.validate.min.js') }}"></script>
    <script src="{{ pathAssets('vendor/summernote/js/summernote.min.js') }}"></script>
@endpush
@push('script')
    <script>
        jQuery(document).ready(function() {
                $(".summernote").summernote({
                    height: 190,
                    minHeight: null,
                    maxHeight: null,
                    focus: !1
                }), $(".inline-editor").summernote({
                    airMode: !0
                })
            }), window.edit = function() {
                $(".click2edit").summernote()
            }, window.save = function() {
                $(".click2edit").summernote("destroy")
            };
     document.addEventListener('DOMContentLoaded', function() {
        // Get all input fields
        var inputs = document.querySelectorAll('input[type="text"], textarea');

        // Function to capitalize the first letter of a string
        function capitalizeFirstLetter(str) {
            return str.charAt(0).toUpperCase() + str.slice(1);
        }

        // Loop through each input field
        inputs.forEach(function(input) {
            input.addEventListener('input', function() {
                this.value = capitalizeFirstLetter(this.value);
            });
        });
    });
    document.querySelector('form').addEventListener('submit', function() {
    this.querySelector('button[type="submit"]').disabled = true;
});
    </script>
@endpush
