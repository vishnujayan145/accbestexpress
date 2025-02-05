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
                             action="{{ route('delivery_voucher.store') }}">


                                {{ csrf_field() }}
                                <div class="voucher-fields">
                                    <div class="row clearfix">
                                        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-4">
                                            <div class="form-group form-float">
                                                <div class="form-line row g-3 col">
                                                    <input name="voucher_id" type="text"
                                                        value="{{ $next_voucher_no ?? 'No Voucher ID Available' }}"
                                                        readonly class="form-control">
                                                    <label class="form-label">Voucher No</label>
                                                </div>
                                            </div>
                                        </div>




                                        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-4">
                                            <div class="form-group form-float">
                                                <div class="form-line row g-3 col">
                                                <input name="ship_no" type="text" value="" class="form-control" required>
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
                                                        <option value="{{ $head->id }}">{{ $head->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-4">
                                            <div class="form-group form-float">
                                                <div class="form-line">
                                                    <input value="<?php echo date('Y-m-d'); ?>" name="date" type="date"
                                                        class="form-control">

                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-4">
                                            <div class="form-group form-float">
                                                <div class="form-line row g-3 col">
                                                    <input name="remarks" type="text" value="" class="form-control" required>
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





                                    <div class="col-lg-1 col-md-1 col-sm-12 col-xs-1">
                                        <div class="form-group form-float">
                                            <div class="form-line row g-3 col">
                                                <input value="" name="weight[]" type="number" oninput="calculateAmount()" class="form-control">

                                                <label class="form-label">Weight</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-1 col-md-1 col-sm-12 col-xs-1">
                                        <div class="form-group form-float">
                                            <div class="form-line row g-3 col">
                                                <input value="" name="rate[]" type="number" oninput="calculateAmount()" class="form-control">

                                                <label class="form-label">Rate</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-2 col-sm-12 col-xs-2">
                                        <div class="form-group form-float">
                                            <div class="form-line row g-3 col">
                                                <input value="" name="amt_clring[]" type="number" oninput="calculateAmount()" class="form-control">
                                                <label class="form-label">AMT Clearing</label>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="col-lg-1 col-md-1 col-sm-12 col-xs-1">
                                        <div class="form-group form-float">
                                            <div class="form-line row g-3 col">
                                                <input value="" name="duty[]" type="number" oninput="calculateAmount()" class="form-control">

                                                <label class="form-label">Duty</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-2 col-sm-12 col-xs-2">
                                        <div class="form-group form-float">
                                            <div class="form-line row g-3 col">
                                                <input value="" name="pcs[]" type="number" class="form-control">
                                                <label class="form-label">Pcs</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-2 col-sm-12 col-xs-2">
                                        <div class="form-group form-float">
                                            <div class="form-line row g-3 col">
                                                <input value="" name="total[]" type="text" class="form-control" readonly>
                                                <label class="form-label">Total</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-1 col-md-1 col-sm-12 col-xs-1">
                                        <div class="form-line">
                                            <button type="submit" class="btn btn-success m-t-15 waves-effect"
                                                id="addButton">
                                                {{ __('root.common.add') }}
                                            </button>
                                        </div>
                                    </div>
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
                                        <!-- Rows will be dynamically added here -->
                                    </tbody>
                                    <tfoot>
                                        <tr>

                                        </tr>
                                    </tfoot>
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
   document.addEventListener("DOMContentLoaded", function () {
    const addButton = document.getElementById("addButton");
    const saveButton = document.getElementById("saveButton");
    const invoiceTableBody = document.querySelector("#invoiceTable tbody");
    const invoiceTableFoot = document.querySelector("#invoiceTable tfoot");
    const addInvoiceForm = document.getElementById("addInvoiceForm");

    // Function to calculate total for a row
    document.querySelectorAll("input[name='weight[]'], input[name='rate[]'], input[name='amt_clring[]'], input[name='duty[]']").forEach(input => {
        input.addEventListener("input", function () {
            calculateAmount(this);
        });
    });

    function calculateAmount(inputElement) {
        let row = inputElement.closest(".form-group").parentElement.parentElement; // Get the parent row
        let weight = parseFloat(row.querySelector("input[name='weight[]']").value) || 0;
        let rate = parseFloat(row.querySelector("input[name='rate[]']").value) || 0;
        let amtClring = parseFloat(row.querySelector("input[name='amt_clring[]']").value) || 0;
        let duty = parseFloat(row.querySelector("input[name='duty[]']").value) || 0;

        let total = (weight * rate) + amtClring + duty;
        row.querySelector("input[name='total[]']").value = total.toFixed(2);
    }

    // Update totals in the invoice table footer
    function updateTotals() {
        let totalPcs = 0, totalWeight = 0, totalRate = 0, totalAmtClring = 0, totalDuty = 0, totalAmount = 0;

        document.querySelectorAll("#invoiceTable tbody tr").forEach(row => {
            totalPcs += parseInt(row.children[1].textContent) || 0;
            totalWeight += parseFloat(row.children[2].textContent) || 0;
            totalRate += parseFloat(row.children[3].textContent) || 0;
            totalAmtClring += parseFloat(row.children[4].textContent) || 0;
            totalDuty += parseFloat(row.children[5].textContent) || 0;
            totalAmount += parseFloat(row.children[6].textContent) || 0;
        });

        let tableFoot = document.querySelector("#invoiceTable tfoot");
        tableFoot.innerHTML = `
            <tr>
                <th>Total</th>
                <th>${totalPcs}</th>
                <th>${totalWeight.toFixed(2)}</th>
                <th>${totalRate.toFixed(2)}</th>
                <th>${totalAmtClring.toFixed(2)}</th>
                <th>${totalDuty.toFixed(2)}</th>
                <th>${totalAmount.toFixed(2)}</th>
            </tr>
        `;
    }

    // Add new invoice row to table
    addButton.addEventListener("click", function (event) {
     event.preventDefault();

     let pcs = parseInt(document.querySelector("input[name='pcs[]']").value) || 0;
     let weight = parseFloat(document.querySelector("input[name='weight[]']").value) || 0;
     let rate = parseFloat(document.querySelector("input[name='rate[]']").value) || 0;
     let amtClring = parseFloat(document.querySelector("input[name='amt_clring[]']").value) || 0;
     let duty = parseFloat(document.querySelector("input[name='duty[]']").value) || 0;

     // Check if pcs or weight is empty, show an alert
      if (pcs <= 0 || weight <= 0) {
        alert("Please enter valid values for Weight and Pieces!");
        return; // Don't add the row if validation fails
     }

     let total = (weight * rate) + amtClring + duty;

     let newRow = document.createElement("tr");
      newRow.innerHTML = `
        <td>Invoice #${invoiceTableBody.children.length + 1}</td>
        <td><input type="hidden" name="pcs[]" value="${pcs}">${pcs}</td>
        <td><input type="hidden" name="weight[]" value="${weight}">${weight.toFixed(2)}</td>
        <td><input type="hidden" name="rate[]" value="${rate}">${rate.toFixed(2)}</td>
        <td><input type="hidden" name="amt_clring[]" value="${amtClring}">${amtClring.toFixed(2)}</td>
        <td><input type="hidden" name="duty[]" value="${duty}">${duty.toFixed(2)}</td>
        <td><input type="hidden" name="total[]" value="${total}">${total.toFixed(2)}</td>
     `;
     invoiceTableBody.appendChild(newRow);
     updateTotals();
     });




    // Handle form submission via AJAX
   // Debugging pcs values before submitting form
    saveButton.addEventListener("click", function (event) {
    event.preventDefault();

    let formData = new FormData(addInvoiceForm);
    let pcsValues = document.querySelectorAll("input[name='pcs[]']");
    pcsValues.forEach(pcs => {
        console.log("PCS Value:", pcs.value);  // Check if pcs values are populated
    });

    fetch(addInvoiceForm.action, {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": document.querySelector("input[name='_token']").value
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("Voucher and invoices added successfully!");
            location.reload();
        } else {
            alert("Error: " + data.error);
        }
    })
    .catch(error => console.error("Error:", error));
 });

 });

</script>


@endpush