@extends('layouts.app')

@section('title')
{{ __('root.reports.agency_voucher_heading') }}
@stop
@section('top-bar')
@include('includes.top-bar')
@stop
@section('left-sidebar')
@include('includes.left-sidebar')
@stop
@section('content')

<style>
    /* Style for Table Head */
    #invoiceTable thead {
        background-color: #60a7d2;
        /* Change this color as needed */
        color: white;
    }

    /* Style for Table Foot */
    #invoiceTable tfoot {
        background-color: #2e78b9;
        /* Change this color as needed */
        color: white;
    }

    /* Optional: Style for Table Rows */
    #invoiceTable tbody tr:nth-child(even) {
        background-color: #f9f9f9;
        /* Light grey for even rows */
    }

    #invoiceTable tbody tr:nth-child(odd) {
        background-color: #ffffff;
        /* White for odd rows */
    }
</style>
<section @if($is_rtl) dir="rtl" @endif class="content">
    <div class="header">
        <h2 class="text-center">{{ __('root.reports.delivery_voucher_heading') }}</h2>

        <br>
    </div>
    <div class="container-fluid">
        <div class="row clearfix">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="header">

                        <div class="body">
                            <form enctype="multipart/form-data" class="form" id="addInvoiceForm" method="post"
                                action="{{ route('delivery_voucher.update', $delivery_voucher->id) }}">


                                {{ csrf_field() }}
                                <div class="voucher-fields">
                                    <div class="row clearfix">
                                        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-4">
                                            <div class="form-group form-float">
                                                <div class="form-line row g-3 col">
                                                    <input name="voucher_id" type="text"
                                                        value="{{ $delivery_voucher->voucher_id ?? 'No Voucher ID Available' }}"
                                                        readonly class="form-control">
                                                    <label class="form-label">Voucher No</label>
                                                </div>
                                            </div>
                                        </div>




                                        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-4">
                                            <div class="form-group form-float">
                                                <div class="form-line row g-3 col">
                                                    <input name="ship_no" type="text" value="{{ $delivery_voucher->ship_no ?? '' }}" class="form-control" required>
                                                    <label class="form-label">Ship No</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-4">
                                            <div class="form-group form-float">
                                                <div class="form-line">
                                                    <select class="form-select form-control" name="party_id" required>
                                                        <option selected></option>
                                                        @foreach ($income_expense_heads as $head)
                                                        <option value="{{ $head->id }}"
                                                            {{ $delivery_voucher->party_id == $head->id ? 'selected' : '' }}>
                                                            {{ $head->name }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-4">
                                            <div class="form-group form-float">
                                                <div class="form-line">
                                                    <input value="{{ $delivery_voucher->date ?? date('Y-m-d') }}" name="date" type="date"
                                                        class="form-control">

                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-4">
                                            <div class="form-group form-float">
                                                <div class="form-line row g-3 col">
                                                    <input name="remarks" type="text" value="{{ $delivery_voucher->remarks ?? '' }}" class="form-control">
                                                    <label class="form-label">Remarks</label>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                            <div class="form-group form-float">

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row invoice-fields">






                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right">
                                        <button type="submit" class="btn btn-primary m-t-15 waves-effect"
                                            id="saveButton">
                                            Save
                                        </button>
                                    </div>


                                </div>
                                <h4>Invoices</h4>
                                <table class="table table-bordered" id="invoiceTable">
    <thead>
        <tr>
            <th>Invoice</th>
            <th>Pieces</th>
            <th>Weight</th>
            <th>Rate</th>
            <th>AMT Clearing</th>
            <th>Duty</th>
            <th>Amount</th>
        </tr>
    </thead>

    <tbody>
        @if($invoice_details->isEmpty())
        <tr>
            <td colspan="7" class="text-center">No invoices found</td>
        </tr>
        @else
        @foreach ($invoice_details as $index => $invoice)
        <tr>
            <td>{{ $invoice->voucher_id }}</td>
            <td><input type="number" name="pcs[]" value="{{ $invoice->pcs ?? 0 }}" class="form-control"></td>
            <td><input type="number" name="weight[]" value="{{ $invoice->weight ?? 0 }}" class="form-control weight"></td>
            <td><input type="number" name="rate[]" value="{{ $invoice->rate ?? 0 }}" class="form-control rate"></td>
            <td><input type="number" name="amt_clring[]" value="{{ $invoice->amt_clring ?? 0 }}" class="form-control amt_clring"></td>
            <td><input type="number" name="duty[]" value="{{ $invoice->duty ?? 0 }}" class="form-control duty"></td>
            <td><input type="text" name="total[]" value="{{ $invoice->total ?? 0 }}" class="form-control total" readonly></td>
        </tr>
        @endforeach
        @endif
    </tbody>
</table>

                            </form>

                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>


    </div>
</section>

<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>

<!-- -->
@stop

@push('include-css')
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
    // Validation and calculation
    var UiController = (function() {
        var DOMString = {
            submit_form: 'form.form',

            start_from: 'input[name=start_from]',
            end_from: 'input[name=end_from]',
        };
        return {
            getDOMString: function() {
                return DOMString;
            },
            getFields: function() {
                return {
                    get_form: document.querySelector(DOMString.submit_form),
                    get_start_from: document.querySelector(DOMString.start_from),
                    get_end_from: document.querySelector(DOMString.end_from),
                }
            },
            getInputsValue: function() {
                var Fields = this.getFields();
                return {
                    start_from: Fields.get_start_from.value == "" ? 0 : Fields.get_start_from,
                    end_from: Fields.get_end_from.value == "" ? 0 : Fields.get_end_from,
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
            if (input_values.start_from == 0) {
                toastr["error"]('Set {{ __('
                    root.reports.period ') }} Date');
                e.preventDefault();
            }
            if (input_values.end_from == 0) {
                toastr["error"]('Set {{ __('
                    root.reports.period ') }} Date');
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    function calculateTotal(row) {
        let weight = parseFloat(row.querySelector('.weight')?.value) || 0;
        let rate = parseFloat(row.querySelector('.rate')?.value) || 0;
        let amtClring = parseFloat(row.querySelector('.amt_clring')?.value) || 0;
        let duty = parseFloat(row.querySelector('.duty')?.value) || 0;

        // Calculate total
        let total = (weight * rate) + amtClring + duty;

        // Update total field
        let totalField = row.querySelector('.total');
        if (totalField) {
            totalField.value = total.toFixed(2);
        }
    }

    // Attach event listeners to all input fields inside the table
    document.querySelectorAll('#invoiceTable tbody tr').forEach(row => {
        row.querySelectorAll('.weight, .rate, .amt_clring, .duty').forEach(input => {
            input.addEventListener('input', function () {
                calculateTotal(row);
            });
        });
    });
});
</script>




@endpush