@extends('layouts.app')

<?php

$moduleName = __('root.contra_voucher.contra_voucher_manage');
$createItemName = __('root.common.create') . ' ' . $moduleName;

$breadcrumbMainName = $moduleName;
$breadcrumbCurrentName = __('root.common.create');

$breadcrumbMainIcon = 'account_balance_wallet';
$breadcrumbCurrentIcon = 'archive';

$ModelName = 'App\Transaction';
$ParentRouteName = 'contra_voucher';

$voucher_type = 'Contra';

$all = config('role_manage.ContraVoucher.All');

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
                @if ($all == 1)
                    <a class="btn btn-sm btn-info waves-effect"
                        href="{{ route($ParentRouteName) }}">{{ __('root.common.all') }}</a>
                @endif
            </div>
            <ol class="breadcrumb breadcrumb-col-cyan @if ($is_rtl) pull-left  @else pull-right @endif">
                <li><a href="{{ route('dashboard') }}"><i class="material-icons">home</i> {{ __('root.common.home') }}</a>
                </li>
                <li><a href="{{ route($ParentRouteName) }}"><i
                            class="material-icons">{{ $breadcrumbMainIcon }}</i>&nbsp;{{ $breadcrumbMainName }}</a>
                </li>
                <li class="active"><i class="material-icons">{{ $breadcrumbCurrentIcon }}</i>&nbsp;
                    {{ $breadcrumbCurrentName }}
                </li>
            </ol>
            <!-- Inline Layout | With Floating Label -->
            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="header">
                            <h2 class="m-b-20">
                                {{ $createItemName }}
                                <small>Put {{ $moduleName }} Information</small>
                            </h2>
                            <div class="body">
                                <form class="form" id="form_validation" method="post"
                                    action="{{ route($ParentRouteName . '.store') }}">
                                    {{ csrf_field() }}
                                    <div class="row clearfix">
                                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 field_area">
                                            <div class="form-group form-float">
                                                <div class="form-line">
                                                    <select data-live-search="true" class="form-control show-tick"
                                                        name="branch_id">
                                                        <option value="0">Select Branch Name</option>
                                                        @foreach ($branches->sortByDesc('id') as $project)
                                                            <option @if ($project->id == old('branch_id')) selected @endif
                                                                value="{{ $project->id }}">{{ $project->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 field_area">
                                            <div class="form-group form-float">
                                                <div class="form-line">
                                                    <select data-live-search="true" class="form-control show-tick"
                                                        name="bank_cash_id" id="">
                                                        <option value="0"> Select Bank Cash Name ( Dr )</option>
                                                        @foreach ($bank_cashes->sortByDesc('id') as $bank_cash)
                                                            <option @if ($bank_cash->id == old('bank_cash_id')) selected @endif
                                                                value="{{ $bank_cash->id }}">{{ $bank_cash->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 field_area">
                                            <div class="form-group form-float">
                                                <div class="form-line">
                                                    <input name="cheque_number" type="text" class="form-control">
                                                    <label class="form-label">Cheque Number</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row dr">
                                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 field_area">
                                            <div class="form-group form-float">
                                                <div class="form-line">
                                                    <select data-live-search="true" class="form-control show-tick"
                                                        name="bank_cash_id_cr" id="">
                                                        <option value="0"> Select Bank Cash Name ( Cr )</option>
                                                        @foreach ($bank_cashes->sortByDesc('id') as $bank_cash)
                                                            <option @if ($bank_cash->id == old('bank_cash_id_cr')) selected @endif
                                                                value="{{ $bank_cash->id }}">{{ $bank_cash->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 field_area">
                                            <div class="form-group form-float">
                                                <div class="form-line">
                                                    <input name="amount" type="number" class="form-control amount">
                                                    <label class="form-label"> Amount </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 field_area">
                                            <div class="form-group form-float">
                                                <div class="form-line" id="bs_datepicker_container">
                                                    <input autocomplete="off" value="{{ old('voucher_date') }}"
                                                        name="voucher_date" type="text" class="form-control"
                                                        placeholder="Please choose a date...">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 field_area">
                                          <div class="form-group form-float">
                                            <div class="form-line" id="">
                                                <input autocomplete="off" value=""
                                                    name="reference_no" type="text" class="form-control"
                                                    placeholder="Please Enter Reference No:">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8 field_area">
                                            <div class="form-group form-float">
                                                <div class="form-line">
                                                    <textarea name="particulars" rows="2" class="form-control no-resize" placeholder="Particulars">{{ old('particulars') }}</textarea>
                                                </div>
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
        </div>
    </section>
@stop

@push('include-css')
    <!-- Bootstrap Select Css -->
    <link href="{{ asset('asset/plugins/bootstrap-select/css/bootstrap-select.css') }}" rel="stylesheet" />
    <!-- Bootstrap Material Datetime Picker Css -->
    <link href="{{ asset('asset/plugins/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css') }}"
        rel="stylesheet" />
    <!-- Bootstrap DatePicker Css -->
    <link href="{{ asset('asset/plugins/bootstrap-datepicker/css/bootstrap-datepicker.css') }}" rel="stylesheet" />
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
        // Validation and calculation on Cr Voucher
        var UiController = (function() {
            var DOMString = {
                submit_form: 'form.form',
                field_area: '.field_area',
                project_id: 'select[name=branch_id]',
                bankcash_id: 'select[name=bank_cash_id]',
                bankcash_id_cr: 'select[name=bank_cash_id_cr]',
                cheque_number: 'input[name=cheque_number]',
                amount: 'input[name=amount]',
                date: 'input[name=voucher_date]',
                particulars: 'textarea[name=particulars]',
                drCloset: '.dr',
            };
            return {
                getDOMString: function() {
                    return DOMString;
                },
                getFields: function() {
                    return {
                        get_form: document.querySelector(DOMString.submit_form),
                        get_project_id: document.querySelector(DOMString.project_id),
                        get_bankcash_id: document.querySelector(DOMString.bankcash_id),
                        get_bankcash_id_cr: document.querySelector(DOMString.bankcash_id_cr),
                        get_cheque_number: document.querySelector(DOMString.cheque_number),
                        get_amount: document.querySelector(DOMString.amount),
                        get_date: document.querySelector(DOMString.date),
                        get_particulars: document.querySelector(DOMString.particulars),
                    }
                },
                getValues: function() {
                    var Fields = this.getFields();
                    return {
                        project_id: Fields.get_project_id.value == "" ? 0 : parseFloat(Fields.get_project_id
                            .value),
                        bankcash_id: Fields.get_bankcash_id.value == "" ? 0 : parseFloat(Fields.get_bankcash_id
                            .value),
                        bankcash_id_cr: Fields.get_bankcash_id_cr.value == "" ? 0 : parseFloat(Fields
                            .get_bankcash_id_cr.value),
                        cheque_number: Fields.get_cheque_number.value == "" ? 0 : parseFloat(Fields
                            .get_cheque_number.value),
                        amount: Fields.get_amount.value == "" ? 0 : parseFloat(Fields.get_amount.value),
                        date: Fields.get_date.value == "" ? 0 : Fields.get_date.value,
                        particulars: Fields.get_particulars.value == "" ? 0 : Fields.get_particulars.value,
                    }
                },

                hide: function(Field) {
                    var DomString = this.getDOMString();
                    var Area = Field.closest(DomString.field_area);
                    if (Area) {
                        Area.style.display = 'none';
                    }
                },
                show: function(Field) {
                    var DomString = this.getDOMString();
                    var Area = Field.closest(DomString.field_area);
                    if (Area) {
                        Field.value = 0;
                        Area.style.display = 'block';
                    }
                },
            }
        })();
        var MainController = (function(UICnt) {
            var DOMString = UICnt.getDOMString();
            var Fields = UICnt.getFields();
            var Values;
            Values = UICnt.getValues();
            var setUpEventListner = function() {
                Fields.get_form.addEventListener('submit', validation);
                Fields.get_bankcash_id.addEventListener('change', function() {
                    bankcashChange(this.value);
                });
                Fields.get_bankcash_id_cr.addEventListener('change', function() {
                    bankcashChange(this.value);
                });
            };
            var validation = function(e) {
                var Values, Fields;
                Values = UICnt.getValues();
                Fields = UICnt.getFields();
                if (Values.project_id == 0) {
                    toastr["error"]('Select  branch name');
                    e.preventDefault();
                }
                if (Values.bankcash_id == 0) {
                    toastr["error"]('Select Bank Cash Name ( Dr )');
                    e.preventDefault();
                }
                if (Values.bankcash_id_cr == 0) {
                    toastr["error"]('Select Bank Cash Name ( Cr )');
                    e.preventDefault();
                }
                if (Values.bankcash_id == Values.bankcash_id_cr && Values.bankcash_id != 0 && Values
                    .bankcash_id_cr != 0) {
                    toastr["error"]('Bank Cash ( Dr ) and Bank Cash ( Cr ) should not same');
                    e.preventDefault();
                }
                if (Values.amount == 0) {
                    toastr["error"]('Amount is required');
                    e.preventDefault();
                }
                if (Values.date == 0) {
                    toastr["error"]('Date is required');
                    e.preventDefault();
                }
            };
            var bankcashChange = function(bankcashID) {
                var DomString = UICnt.getDOMString();
                var Area = Fields.get_cheque_number.closest(DomString.field_area);
                if (Area.style.display == 'none') {
                    if (bankcashID <= 1) {
                        UICnt.hide(Fields.get_cheque_number);
                    } else {
                        UICnt.show(Fields.get_cheque_number);
                    }
                }
            };
            return {
                init: function() {
                    console.log("App Is running");
                    setUpEventListner();
                    // Default hide fields
                    var Fields = UICnt.getFields();
                    UICnt.hide(Fields.get_cheque_number);
                }
            }

        })(UiController);
        MainController.init();
    </script>
@endpush
