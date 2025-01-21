@extends('layouts.app')

@section('title')
{{ __('root.reports.ledger_manage') }}
@stop
@section('top-bar')
@include('includes.top-bar')
@stop
@section('left-sidebar')
@include('includes.left-sidebar')
@stop
@section('content')
<section @if($is_rtl) dir="rtl" @endif class="content">
    <div class="header">
        <h2 class="text-center">{{ __('root.reports.ledger_manage') }}</h2>
    </div>
    <div class="container-fluid">
        <!-- Inline Layout | With Floating Label -->
        <div class="row clearfix">
            <!-- Branch Wise Start -->
            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                <div class="card">
                    <div class="header bg-cyan">
                        <h2>
                            {{ __('root.reports.branch_wise') }}
                            <small>{{ __('root.reports.show_all') }}</small>
                        </h2>
                    </div>
                    <br>
                    <div class="body">
                        <div class="row clearfix">
                            <form class="form" id="form_validation" method="post"
                                action="{{ route('reports_accounts_ledger.branch_wise.report') }}">
                                {{ csrf_field() }}
                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <select data-live-search="true" class="form-control show-tick"
                                                name="branch_id">
                                                <option value="">{{ __('root.reports.select_branch_name') }}</option>
                                                @foreach ($branches as $Branch)
                                                <option value="{{ $Branch->id }}">{{ $Branch->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <select data-live-search="true" class="form-control show-tick"
                                                name="income_expense_head_id">
                                                <option value="">{{ __('root.reports.select_ledger_name') }}</option>
                                                @foreach ($income_expense_heads as $income_expense_head)
                                                <option value="{{ $income_expense_head->id }}">
                                                    {{ $income_expense_head->name }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="input-daterange input-group" id="bs_datepicker_range_container">
                                        <div class="form-line">
                                            <input autocomplete="off" name="from" type="text" class="form-control"
                                                placeholder="{{ __('root.reports.date_start') }}...">
                                        </div>
                                        <span class="input-group-addon">{{ __('root.reports.to') }}</span>
                                        <div class="form-line">
                                            <input autocomplete="off" name="to" type="text" class="form-control"
                                                placeholder="{{ __('root.reports.date_end') }}...">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-line">
                                        <input formtarget="_blank" name="action" value="Show" type="submit"
                                            class="btn btn-primary m-t-15 waves-effect">
                                        {{--<input name="action" value="Pdf" type="submit"
                                                class="btn btn-primary m-t-15 waves-effect">--}}
                                        <input name="action" value="Excel" type="submit"
                                            class="btn btn-primary m-t-15 waves-effect">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Branch Wise End -->
            <!-- Income Expense Head  Wise Start -->
            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                <div class="card">
                    <div class="header bg-cyan">
                        <h2>
                            {{ __('root.reports.ledger_name_wise') }}
                            <small>{{ __('root.reports.show_all') }}</small>
                        </h2>
                    </div>
                    <br>
                    <div class="body">
                        <div class="row clearfix">
                            <form class="form" id="form_validation" method="post"
                                action="{{ route('reports_accounts_ledger.income_expense_head_wise.report') }}">
                                {{ csrf_field() }}
                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <select data-live-search="true" class="form-control show-tick"
                                                name="income_expense_head_id">
                                                <option value="">{{ __('root.reports.select_ledger_name') }}</option>
                                                @foreach ($income_expense_heads as $income_expense_head)
                                                <option value="{{ $income_expense_head->id }}">
                                                    {{ $income_expense_head->name }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <select data-live-search="true" class="form-control show-tick"
                                                name="branch_id">
                                                <option value="">{{ __('root.reports.select_branch_name') }}</option>
                                                @foreach ($branches as $Branch)
                                                <option value="{{ $Branch->id }}">{{ $Branch->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="input-daterange input-group" id="bs_datepicker_range_container1">
                                        <div class="form-line">
                                            <input autocomplete="off" name="from" type="text" class="form-control"
                                                placeholder="{{ __('root.reports.date_start') }}...">
                                        </div>
                                        <span class="input-group-addon">{{ __('root.reports.to') }}</span>
                                        <div class="form-line">
                                            <input autocomplete="off" name="to" type="text"
                                                class="form-control" placeholder="{{ __('root.reports.date_end') }}...">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-line">
                                        <input formtarget="_blank" name="action" value="Show" type="submit"
                                            class="btn btn-primary m-t-15 waves-effect">
                                        {{--<input name="action" value="Pdf" type="submit"
                                                    class="btn btn-primary m-t-15 waves-effect">--}}
                                        <input name="action" value="Excel" type="submit"
                                            class="btn btn-primary m-t-15 waves-effect">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Income Expense Head  End -->
            <!--Cash Bank Book Start -->
            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                <div class="card">
                    <div class="header bg-cyan">
                        <h2>
                            {{ __('root.reports.cash_bank_book_wise') }}
                            <small>{{ __('root.reports.show_all') }}</small>
                        </h2>
                    </div>
                    <br>
                    <div class="body">
                        <div class="row clearfix">
                            <form class="form" id="form_validation" method="post"
                                action="{{ route('reports_accounts_ledger.bank_cash_wise.report') }}">
                                {{ csrf_field() }}
                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <select data-live-search="true" class="form-control show-tick"
                                                name="bank_cash_id" required>
                                                <option value="">{{ __('root.reports.select_bank_cash_name') }}</option>
                                                @foreach ($bank_cashes as $BankCash)
                                                <option value="{{ $BankCash->id }}">{{ $BankCash->name }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <select data-live-search="true" class="form-control show-tick"
                                                name="branch_id">
                                                <option value="">{{ __('root.reports.select_branch_name') }}</option>
                                                @foreach ($branches as $project)
                                                <option @if ($project->id == old('branch_id')) selected @endif
                                                    value="{{ $project->id }}">{{ $project->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="input-daterange input-group" id="bs_datepicker_range_container2">
                                        <div class="form-line">
                                            <input autocomplete="off" name="from" type="text"
                                                class="form-control" placeholder="{{ __('root.reports.date_start') }}...">
                                        </div>
                                        <span class="input-group-addon">{{ __('root.reports.to') }}</span>
                                        <div class="form-line">
                                            <input autocomplete="off" name="to" type="text"
                                                class="form-control" placeholder="{{ __('root.reports.date_end') }}...">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-line">
                                        <input formtarget="_blank" name="action" value="Show" type="submit"
                                            class="btn btn-primary m-t-15 waves-effect">
                                        {{--<input name="action" value="Pdf" type="submit"
                                                    class="btn btn-primary m-t-15 waves-effect">--}}
                                        <input name="action" value="Excel" type="submit"
                                            class="btn btn-primary m-t-15 waves-effect">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Branch Wise End -->

            <!--Cash Receivables Start -->
            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                <div class="card">
                    <div class="header bg-cyan">
                        <h2>
                            {{ __('root.reports.cash_receivables') }}
                            <small>{{ __('root.reports.show_all') }}</small>
                        </h2>
                    </div>
                    <br>
                    <div class="body">
                        <div class="row clearfix">
                            <form class="form" id="form_validation" method="post"
                                action="{{ route('reports_accounts_ledger.cash_receivables.report') }}">
                                {{ csrf_field() }}
                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <select data-live-search="true" class="form-control show-tick"
                                                name="income_expense_head_id">
                                                <option value="">{{ __('root.reports.select_ledger_name') }}</option>
                                                @foreach ($shipments as $income_expense_head)
                                                <option value="{{ $income_expense_head->sender_id }}">
                                                    {{ $income_expense_head->sender }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <select data-live-search="true" class="form-control show-tick"
                                                name="branch_id">
                                                <option value="">{{ __('root.reports.select_branch_name') }}</option>
                                                @foreach ($branches as $project)
                                                <option @if ($project->id == old('branch_id')) selected @endif
                                                    value="{{ $project->id }}">{{ $project->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="input-daterange input-group" id="bs_datepicker_range_container3">
                                        <div class="form-line">
                                            <input autocomplete="off" name="from" type="text"
                                                class="form-control" placeholder="{{ __('root.reports.date_start') }}...">
                                        </div>
                                        <span class="input-group-addon">{{ __('root.reports.to') }}</span>
                                        <div class="form-line">
                                            <input autocomplete="off" name="to" type="text"
                                                class="form-control" placeholder="{{ __('root.reports.date_end') }}...">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-line">
                                        <input formtarget="_blank" name="action" value="Show" type="submit"
                                            class="btn btn-primary m-t-15 waves-effect">
                                        {{--<input name="action" value="Pdf" type="submit"
                                                    class="btn btn-primary m-t-15 waves-effect">--}}
                                        <input name="action" value="Excel" type="submit"
                                            class="btn btn-primary m-t-15 waves-effect">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Cash Receivables End -->
        </div>
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
<!-- noUISlider Css -->
<link href="{{ asset('asset/plugins/nouislider/nouislider.min.css') }}" rel="stylesheet" />
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
<!-- Sweet Alert Plugin Js -->
<script src="{{ asset('asset/plugins/sweetalert/sweetalert.min.js') }}"></script>
<!-- Autosize Plugin Js -->
<script src="{{ asset('asset/plugins/autosize/autosize.js') }}"></script>
<script src="{{ asset('asset/js/pages/forms/basic-form-elements.js') }}"></script>
<script>
    @if(Session::has('success'))
    toastr["success"]('{{ Session::get('
        success ') }}');
    @endif
    @if(Session::has('error'))
    toastr["error"]('{{ Session::get('
        error ') }}');
    @endif
    @if($errors -> any())
    @foreach($errors -> all() as $error)
    toastr["error"]('{{ $error }}');
    @endforeach
    @endif
</script>
@endpush