@extends('layouts.app')

{{--Important Variables--}}

<?php
$moduleName = __('root.user.user_manage');
$createItemName = __('root.common.create') . ' ' . $moduleName;

$breadcrumbMainName = $moduleName;
$breadcrumbCurrentName = __('root.common.create');

$breadcrumbMainIcon = "fas fa-user";
$breadcrumbCurrentIcon = "archive";

$ModelName = 'App\User';
$ParentRouteName = 'user';

?>

@section('title')
    {{ $moduleName }}->{{ $createItemName }}
@stop

@section('top-bar')
    @include('includes.top-bar')
@stop
@section('left-sidebar')
    @include('includes.left-sidebar')
@stop
@section('content')

    <section @if($is_rtl) dir="rtl" @endif class="content">
        <div class="container-fluid">
            <div class="block-header @if ($is_rtl) pull-right @else pull-left @endif">
                <a class="btn btn-sm btn-info  waves-effect" href="{{ url()->previous() }}">{{ __('root.common.back') }}</a>
            </div>

            <ol class="breadcrumb breadcrumb-col-cyan @if ($is_rtl) pull-left  @else pull-right @endif">
                <li><a href="{{ route('dashboard') }}"><i class="material-icons">home</i> {{ __('root.common.home') }}</a></li>
                <li><a href="{{ route($ParentRouteName) }}"><i
                                class="{{ $breadcrumbMainIcon }}"></i> &nbsp;{{  $breadcrumbMainName }}</a>
                </li>
                <li class="active"><i
                            class="material-icons">{{ $breadcrumbCurrentIcon  }}</i> {{ $breadcrumbCurrentName  }}</li>
            </ol>

            <!-- Inline Layout | With Floating Label -->
            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="header">
                            <h2>
                                {{ $createItemName  }}
                                <small>Put {{ $moduleName  }} Information</small>
                            </h2>

                            <div class="body">
                                <form class="form" id="form_validation" method="post"
                                      action="{{ route($ParentRouteName.'.store') }}">

                                    {{ csrf_field() }}
                                    <div class="row clearfix">

                                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">
                                            <div class="form-group form-float">
                                                <div class="form-line">
                                                    <input autofocus value="{{ old('name')  }}" name="name" type="text"
                                                           class="form-control">
                                                    <label class="form-label">Name</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">
                                            <div class="form-group form-float">
                                                <div class="form-line">
                                                    <input value="{{ old('email')  }}" name="email" type="email"
                                                           class="form-control">
                                                    <label class="form-label">Email Address</label>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">
                                            <div class="form-group form-float">
                                                <div class="form-line">
                                                    <input name="password" type="password" class="form-control "
                                                    >
                                                    <label class="form-label">Password</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">
                                            <div class="form-group form-float">
                                                <div class="form-line">
                                                    <input name="password_confirmation" type="password" class="form-control"
                                                    >
                                                    <label class="form-label">Confirm Password</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">
                                            <div class="form-line">
                                                <select data-live-search="true" class="form-control show-tick"
                                                        name="role_manage_id"
                                                        id="role_manage_id">
                                                    <option value="0">Select User Role</option>
                                                    @foreach(App\RoleManage::orderBy('created_at','desc')->get() as $role)
                                                        <option value="{{ $role->id  }}">{{ $role->name  }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                            <div class="form-line">
                                                <button type="submit" class="btn btn-primary m-t-15 waves-effect">
                                                    {{ __('root.common.save') }}
                                                </button>
                                            </div>
                                        </div>


                                    </div>

                                </form>

                            </div>
                        </div>
                    </div>
                </div>
                <!-- #END# Inline Layout | With Floating Label -->
            </div>
    </section>

@stop

@push('include-css')

    <!-- Colorpicker Css -->
    <link href="{{ asset('asset/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.css') }}" rel="stylesheet"/>

    <!-- Dropzone Css -->
    <link href="{{ asset('asset/plugins/dropzone/dropzone.css') }}" rel="stylesheet">

    <!-- Multi Select Css -->
    <link href="{{ asset('asset/plugins/multi-select/css/multi-select.css') }}" rel="stylesheet">

    <!-- Bootstrap Spinner Css -->
    <link href="{{ asset('asset/plugins/jquery-spinner/css/bootstrap-spinner.css') }}" rel="stylesheet">

    <!-- Bootstrap Tagsinput Css -->
    <link href="{{ asset('asset/plugins/bootstrap-tagsinput/bootstrap-tagsinput.css') }}" rel="stylesheet">

    <!-- Bootstrap Select Css -->
    <link href="{{ asset('asset/plugins/bootstrap-select/css/bootstrap-select.css') }}" rel="stylesheet"/>



    <!-- Bootstrap Material Datetime Picker Css -->
    <link href="{{ asset('asset/plugins/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css') }}"
          rel="stylesheet"/>

    <!-- Bootstrap DatePicker Css -->
    <link href="{{ asset('asset/plugins/bootstrap-datepicker/css/bootstrap-datepicker.css') }}" rel="stylesheet"/>


    <!-- noUISlider Css -->
    <link href="{{ asset('asset/plugins/nouislider/nouislider.min.css') }}" rel="stylesheet"/>

    <!-- Sweet Alert Css -->
    <link href="{{ asset('asset/plugins/sweetalert/sweetalert.css') }}" rel="stylesheet"/>


@endpush

@push('include-js')


    <!-- Moment Plugin Js -->
    <script src="{{ asset('asset/plugins/momentjs/moment.js') }}"></script>

    <!-- Bootstrap Material Datetime Picker Plugin Js -->
    <script src="{{ asset('asset/plugins/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js') }}"></script>

    <!-- Bootstrap Datepicker Plugin Js -->
    <script src="{{ asset('asset/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js') }}"></script>


    <!-- Sweet Alert Plugin Js -->
    <script src="{{ asset('asset/plugins/sweetalert/sweetalert.min.js') }}"></script>


    <!-- Autosize Plugin Js -->
    <script src="{{ asset('asset/plugins/autosize/autosize.js') }}"></script>

    <script src="{{ asset('asset/js/pages/forms/basic-form-elements.js') }}"></script>


    <script>
        @if(Session::has('success'))
            toastr["success"]('{{ Session::get('success') }}');
        @endif

                @if(Session::has('error'))
            toastr["error"]('{{ Session::get('error') }}');
        @endif

                @if ($errors->any())
                @foreach ($errors->all() as $error)
            toastr["error"]('{{ $error }}');
        @endforeach
        @endif



        // Validation and calculation
        var UiController = (function () {

            var DOMString = {
                submit_form: 'form.form',
                name: 'input[name=name]',
                email: 'input[name=email]',
                password: 'input[name=password]',
                confirm_password: 'input[name=password_confirmation]',
                role_manage_id: 'role_manage_id',


            };

            return {
                getDOMString: function () {
                    return DOMString;
                },
                getFields: function () {
                    return {
                        get_form: document.querySelector(DOMString.submit_form),
                        get_name: document.querySelector(DOMString.name),
                        get_password: document.querySelector(DOMString.password),
                        get_confirm_password: document.querySelector(DOMString.confirm_password),
                        get_email: document.querySelector(DOMString.email),
                        get_role_manage_id: document.getElementById(DOMString.role_manage_id),


                    }
                },
                getInputsValue: function () {
                    var Fields = this.getFields();
                    return {

                        name: Fields.get_name.value == "" ? 0 : Fields.get_name.value,
                        email: Fields.get_email.value == "" ? 0 : Fields.get_email.value,
                        password: Fields.get_password.value,
                        confirm_password: Fields.get_confirm_password.value,
                        role_manage_id: Fields.get_role_manage_id.value == "" ? 0 : Fields.get_role_manage_id.value,


                    }
                },

            }
        })();

        var MainController = (function (UICnt) {

            var DOMString = UICnt.getDOMString();
            var Fields = UICnt.getFields();

            var setUpEventListner = function () {

                Fields.get_form.addEventListener('submit', validation);

            };

            var validation = function (e) {
                var input_values, Fields;
                input_values = UICnt.getInputsValue();
                Fields = UICnt.getFields();

                var FieldName1 = " Name";


                if (input_values.role_manage_id == 0) {
                    toastr["error"]('Select Any Role');
                    e.preventDefault();
                }


                if (input_values.password != input_values.confirm_password) {
                    toastr["error"]('Password Does Not Match');
                    e.preventDefault();
                }

                if (input_values.email == 0) {
                    toastr["error"]('Email Is Required');
                    e.preventDefault();
                }

                if (input_values.name == 0) {
                    toastr["error"]('Name is Required');
                    e.preventDefault();
                }


            };

            return {
                init: function () {
                    console.log("App Is running");
                    setUpEventListner();

                }
            }

        })(UiController);

        MainController.init();


    </script>

@endpush
