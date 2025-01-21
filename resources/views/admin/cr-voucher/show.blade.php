@extends('layouts.app')

{{--Important Variables--}}

<?php

$moduleName = " Cr Voucher";
$createItemName = "Show" . $moduleName;

$breadcrumbMainName = $moduleName;
$breadcrumbCurrentName = " Show";

$breadcrumbMainIcon = "account_balance_wallet";
$breadcrumbCurrentIcon = "archive";

$ModelName = 'App\Transaction';
$ParentRouteName = 'cr_voucher';
$transaction = new \App\Transaction();

?>

<style>
        body
        {
            font-family:Tahoma;
        }
        table {
    width: 100%;
    border-collapse: collapse;
    
}
.text-right h3,h2,p{
  margin-right:20px !important;
}

thead th {
    background-color: #214597;
    color: #fff;
}


img { 
        max-width: 100%; /* Ensure images are scaled properly */
    }
    #print_button button{
    position: fixed;
    top: 20px;
    right:1050px;
    padding: 10px;
    font-size: 18px;
    background-color: #00ffee;
}
/* Header Styling */
.logo {
   
    width: 50%;
}


h2 {
    color: #214597 !important;
    font-weight: bold;
    font-size: 28px;
    margin-bottom: 10px;
    text-align:right;
}
h1{
  color:#ec2026 !important ;
  text-align:right;
  margin-right:20px;

}
p {
    color: #555;
    font-size: 16px;
    line-height: 1.5;
    text-align:right;
  
}

#print_button {
    margin-top: 10px;
    margin-right:20px;
}

.btn-primary {
    background-color: #007bff;
    border-color: #007bff;
    padding: 10px 20px;
    font-size: 16px;
    font-weight: bold;
    color: #fff;
    cursor: pointer;
}

.btn-primary:hover {
    background-color: #0056b3;
    border-color: #004085;
}
@media (max-width: 767.98px) {
 
  .col-md-6 {
        width: 50% !important;
    }
    .col-md-6 {
        float:left !important;
    }
    h2 {
    color: #214597 !important;
    font-weight: bold;
    font-size: 14px !important;
    margin-bottom: 10px;
}
h1{
  color:#ec2026 !important ;
 font-size:25px !important;
}
   
}

@media print {
  @page {
        size: auto; /* Adjust to fit the content */
        margin: 0; /* No margin */
    }
   
    
    /* You can target specific rows if necessary */
   #trtop {
        font-size: 10px !important; /* Smaller font size for table rows */
    }
    
    body {
        margin: 0; /* Removes body margin */
        padding: 0; /* Removes body padding */
    }
    .container {
        width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    #print_button, .mt-3, .table3{
        display: none;
    }
    th {
        background: #214597 !important;
        color: #fff !important;
        -webkit-print-color-adjust: exact; /* Chrome, Safari */
        print-color-adjust: exact; /* Firefox */
    }
    tr {
        background-color: #214597 !important;
    }

    .header, table {
        page-break-inside: avoid;
    }
    .text-right {
                text-align: right !important;
            }
            h2 {
    
    font-size: 13px !important;
            }
}
</style>
@section('title')
    {{ $moduleName }}->{{ $createItemName }}
@stop
@section('content')
<body data-layout="horizontal">

<!-- Begin page -->
<div id="wrapper">

<div class="content-page1" id="content-page">
 <div class="content">
    <div class="container-fluid">
       
        <div class="row align-items-center" style="padding-bottom: 20px;">
        <div class="col-md-6">
            <!-- Logo Section (to be updated dynamically) -->
            <img id="agencyLogo" src="{{  asset(config('settings.company_logo')) }}" alt="Bestexpress" class="img-fluid logo">
        </div>
        
    <div class="col-md-6" >
        <!-- Agency Name and Address (to be updated dynamically) -->
      
        <h2>
        {{ config('settings.company_name') }} ({{ config('settings.address_1') }})</h2>
       
          <p>  <strong>Date:</strong>  {{ date('d M Y') }}<br>
           
        </p>
 
        <button type="button" onclick="window.print()" class="btn btn-primary" id="print_button" 
            style="background-color: #007bff; border: none; border-radius: 5px; 
                   padding: 10px 20px; float: right;">
        <i class="fas fa-print" style="margin-right: 8px;"></i>Print
    </button>

       
    </div>
</div>

            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <h1 style="text-align:center;text-decoration: underline;text-transform: uppercase;">{{ $moduleName }} </h1>
                    <div class="card">
                       
                  
                        <div class="body">
                            <table class="table  table-bordered table-hover">
                                <thead>
                                <tr id="trtop">
                                    <td> Branch Name: &nbsp; <span class="text-bold"
                                        >{{ App\Transaction::find($items[0]->id)->Branch->name }}</span>
                                    </td>
                                    <td> Made Of Payment: &nbsp; <span class="text-bold"
                                        >{{ App\Transaction::find($items[0]->id)->BankCash->name }}</span>
                                    </td>
                                    <td>Particulars: &nbsp; <span class="text-bold"
                                        >{{ $items[0]->particulars  }}</span></td>
                                    <td> Voucher Date: &nbsp; <span class="text-bold"
                                        >{{ date(config('settings.date_format'), strtotime($items[0]->voucher_date)) }}</span>
                                    </td>
                                </tr>
                                </thead>
                            </table>
                        </div>

                        <div class="body">
                            <table class="table  table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th style="background-color: #214597 !important; color: #fff;">S.L No</th>
                                    <th style="background-color: #214597 !important; color: #fff;">Head Of Account Name</th>
                                    <th style="background-color: #214597 !important; color: #fff;">CHQ. No</th>
                                    <th style="background-color: #214597 !important; color: #fff;">Amount ( <?php echo (config('settings.is_code') == 'code') ?
                                            config('settings.currency_code') : config('settings.currency_symbol')  ?>
                                        )
                                </tr>
                                </thead>
                                <tbody>
                                <?php $total_amount = 0; ?>
                                @foreach($items as $key=>$item)

                                    <?php
                                    $amount = $item->cr;
                                    if ($amount <= 0) {
                                        $amount = $item->dr;
                                    }
                                    $total_amount += $amount;

                                    ?>

                                    <tr>
                                        <td class="text-center">{{ $key+1  }}</td>
                                        <td>{{ App\Transaction::find($item->id)->IncomeExpenseHead->name }}</td>
                                        <td>{{ $item->cheque_number }}</td>
                                        <td> {{ $transaction->convert_money_format($amount)  }}</td>

                                    </tr>
                                @endforeach
                                @if ($total_amount>0)
                                    <tr>
                                        <th colspan="3" class="text-right" style="background-color: #bdb8b8 !important; color: #ec2026 !important ;">Total=</th>
                                        <th  style="background-color: #bdb8b8 !important; color: #ec2026  !important;">{{ $transaction->convert_money_format($total_amount)  }}-/</th>

                                    </tr>
                                @endif

                                </tbody>
                            </table>
                        </div>

                        <div class="body table3">
                            <table class="table  table-bordered table-hover">
                                <thead>
                                <tr>
                                    <td>Created By: &nbsp; <span class="text-bold">{{ $items[0]->created_by  }}</span></td>
                                    <td>Created at: &nbsp; <span class="text-bold">{{ date(config('settings.date_format')." h:i:s", strtotime($items[0]->created_at)) }}</span>
                                    </td>
                                    <td>Deleted By: &nbsp; <span class="text-bold">{{ $items[0]->deleted_by }}</span></td>
                                    <td>Modified by: &nbsp; <span class="text-bold">{{ $items[0]->updated_by  }}</span></td>
                                    <td>Modified at: &nbsp; <span class="text-bold">{{ date(config('settings.date_format')." h:i:s", strtotime($items[0]->updated_at))  }}</span>
                                    </td>
                                </tr>
                                </thead>
                            </table>
                        </div>

                    </div>
                </div>
                <!-- #END# Inline Layout | With Floating Label -->
            </div>

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



@endpush

