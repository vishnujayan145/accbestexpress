@extends('layouts.app')

<?php

$moduleName = ' Language manage';
$createItemName = 'Configuration' . $moduleName;

$breadcrumbMainName = $moduleName;
$breadcrumbCurrentName = ' '.__('root.common.all');

$breadcrumbMainIcon = 'fas fa-language';
$breadcrumbCurrentIcon = 'archive';

$ModelName = 'App\Language';
$ParentRouteName = 'language';

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
    <section @if ($is_rtl) dir="rtl" @endif class="content">
        <div class="container-fluid">
            <div class="block-header @if ($is_rtl) pull-right @else pull-left @endif">
                <a class="btn btn-sm btn-info waves-effect" href="{{ url()->previous() }}">{{ __('root.common.back') }}</a>
            </div>
            <ol class="breadcrumb breadcrumb-col-cyan @if ($is_rtl) pull-left  @else pull-right @endif">
                <li><a href="{{ route($ParentRouteName) }}"><i class="material-icons">home</i> {{ __('root.common.home') }}</a></li>
                <li><a href="{{ route($ParentRouteName) }}"><i
                            class="{{ $breadcrumbMainIcon }}"></i> &nbsp;{{ $breadcrumbMainName }}</a>
                </li>
                <li class="active"><i class="material-icons">{{ $breadcrumbCurrentIcon }}</i>&nbsp; {{ $breadcrumbCurrentName }}
                </li>
            </ol>
            <!-- Inline Layout | With Floating Label -->
            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="header">
                            <h2>
                                Language Mange for country <b>{{ $language->name }}</b> and code is <b>{{ $language->code }}</b>
                                <small>Configuration {{ $moduleName }} Information </small>
                            </h2>
                        </div>
                        <div class="body">
                            <form class="form" id="form_validation" method="post"
                                action="{{ route($ParentRouteName . '.configureLang', ['language' => $language->id]) }}">
                                {{ csrf_field() }}
                                <div class="panel-group" id="accordion_1" role="tablist" aria-multiselectable="true">
                                    @foreach ($root_file_items as $key => $root_files_item)
                                        <div class="panel panel-primary">
                                            <div class="panel-heading" role="tab"
                                                id="language_menu_{{ $key }}">
                                                <h4 class="panel-title">
                                                    <a role="button" data-toggle="collapse" data-parent="#accordion_1"
                                                        href="#collapse_{{ $key }}"
                                                        @if ($loop->first) aria-expanded="true"
                                                    @else
                                                    aria-expanded="false" @endif
                                                        aria-controls="collapse_{{ $key }}">
                                                        {{ ucfirst(str_replace('_', ' ',$key)) }}
                                                    </a>
                                                </h4>
                                            </div>
                                            <div id="collapse_{{ $key }}"
                                                class="panel-collapse collapse @if ($loop->first) in @endif"
                                                role="tabpanel" aria-labelledby="language_menu_{{ $key }}">
                                                <div class="panel-body">
                                                    <div class="row p-t-10">
                                                        @foreach ($root_files_item as $keyD => $root_file_item)
                                                            @php
                                                                $input_name = $key . "[" . $keyD . "]";
                                                                if (isset($current_file_items[$key]) && isset($current_file_items[$key][$keyD])) {
                                                                    $current_file_value = $current_file_items[$key][$keyD];
                                                                } else {
                                                                    $current_file_value = $root_file_item;
                                                                }
                                                            @endphp
                                                            <div class="col-md-4">
                                                                <div class="form-group form-float">
                                                                    <div class="form-line">
                                                                        <input value="{{ $current_file_value }}" name="{{ $input_name }}"
                                                                            type="text" class="form-control">
                                                                        <label
                                                                            class="form-label">{{ $root_file_item }}</label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="form-line p-b-30">
                                    <button type="submit" class="btn btn-primary m-t-15 waves-effect">
                                        Update
                                    </button>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
                <!-- #END# Inline Layout | With Floating Label -->
            </div>
    </section>
@stop

@push('include-css')
    <!-- Colorpicker Css -->
    <link href="{{ asset('asset/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.css') }}" rel="stylesheet" />
    <!-- Dropzone Css -->
    <link href="{{ asset('asset/plugins/dropzone/dropzone.css') }}" rel="stylesheet">
    <!-- Multi Select Css -->
    <link href="{{ asset('asset/plugins/multi-select/css/multi-select.css') }}" rel="stylesheet">
    <!-- Bootstrap Spinner Css -->
    <link href="{{ asset('asset/plugins/jquery-spinner/css/bootstrap-spinner.css') }}" rel="stylesheet">
    <!-- Bootstrap Tagsinput Css -->
    <link href="{{ asset('asset/plugins/bootstrap-tagsinput/bootstrap-tagsinput.css') }}" rel="stylesheet">
    <!-- Bootstrap Select Css -->
    <link href="{{ asset('asset/plugins/bootstrap-select/css/bootstrap-select.css') }}" rel="stylesheet" />
    <!-- Bootstrap Material Datetime Picker Css -->
    <link href="{{ asset('asset/plugins/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css') }}"
        rel="stylesheet" />
    <!-- Bootstrap DatePicker Css -->
    <link href="{{ asset('asset/plugins/bootstrap-datepicker/css/bootstrap-datepicker.css') }}" rel="stylesheet" />
    <!-- Sweet Alert Css -->
    <link href="{{ asset('asset/plugins/sweetalert/sweetalert.css') }}" rel="stylesheet" />
@endpush

@push('include-js')
    <!-- Moment Plugin Js -->
    <script src="{{ asset('asset/plugins/momentjs/moment.js') }}"></script>
    <!-- Bootstrap Material Datetime Picker Plugin Js -->
    <script src="{{ asset('asset/plugins/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js') }}">
    </script>
    <!-- Bootstrap Datepicker Plugin Js -->
    <script src="{{ asset('asset/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js') }}"></script>
    <!-- Autosize Plugin Js -->
    <script src="{{ asset('asset/plugins/autosize/autosize.js') }}"></script>
    <script src="{{ asset('asset/js/pages/forms/basic-form-elements.js') }}"></script>
    <script>
        @if (Session::has('success'))
            toastr["success"]('{{ Session::get('success') }}');
        @endif
        @if (Session::has('error'))
            toastr["error"]('{{ Session::get('error') }}');
        @endif
        @if ($errors->any())
            @foreach ($errors->all() as $error)
                toastr["error"]('{{ $error }}');
            @endforeach
        @endif
        // Validation and calculation
        var UiController = (function() {
            var DOMString = {
                submit_form: 'form.form',
                name: 'input[name=name]',
            };
            return {
                getDOMString: function() {
                    return DOMString;
                },
                getFields: function() {
                    return {
                        get_form: document.querySelector(DOMString.submit_form),
                        get_name: document.querySelector(DOMString.name),
                    }
                },
                getInputsValue: function() {
                    var Fields = this.getFields();
                    return {
                        name: Fields.get_name.value == "" ? 0 : Fields.get_name.value,
                    }
                },

            }
        })();

        var MainController = (function(UICnt) {

            var DOMString = UICnt.getDOMString();
            var Fields = UICnt.getFields();

            var setUpEventListner = function() {

                Fields.get_form.addEventListener('submit', validation);

            };

            var validation = function(e) {
                var input_values, Fields;
                input_values = UICnt.getInputsValue();
                Fields = UICnt.getFields();

                var FieldName1 = " Name";


                if (input_values.name == 0) {
                    toastr["error"]('Set Any' + FieldName1);
                    e.preventDefault();
                }
            };

            return {
                init: function() {
                    console.log("App Is running");
                    setUpEventListner();

                }
            }

        })(UiController);

        MainController.init();
    </script>
@endpush
