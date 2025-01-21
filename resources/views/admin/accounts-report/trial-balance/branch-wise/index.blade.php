@extends('layouts.pdf')
@push('include-css')
    <link rel="stylesheet" href="{{ asset('asset/css/main-report.css') }}">
@endpush
@section('title')
    {{ config('settings.company_name') }} -> {{ $extra['module_name'] }}
@endsection
@section('content')
 
<style>
        h2 {
      font-weight: 600;
      color: #214597;
      margin: 5px 0;
    }

    h4,h5{
      font-weight: 600;
      margin: 5px 0;
      color:#ec2026;
      margin-right:20px !important;
    }

    /* Table Styling */
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }

    th, td {
      border: 1px solid #999;
      padding: 8px;
    }

    thead th {
      background-color:  #214597;
      color: #fff;
    }

    .right-align {
      text-align: right;
    }

    .dr {
      color: red;
    }

    .cr {
      color: green;
    }

    #hoa, #par {
      width: 20%; /* Adjust the width as needed */
      word-wrap: break-word; /* Ensure long words wrap */
      word-break: break-word; /* Prevent overflow */
    }

    .table-sm td, .table-sm th {
      padding: 0.5rem;
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
.col-md.6{
    padding-right:0px !important;
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
.col-md.6{
    padding-right:0px !important;
   }
}

@media print {
  @page {
        size: auto; /* Adjust to fit the content */
        margin: 0; /* No margin */
    }
   .col-md.6{
    padding-right:0px !important;
   }
    h2,h5,p{
      margin-right:0px !important;
    }
    /* You can target specific rows if necessary */
   #trtop {
        font-size: 10px !important; /* Smaller font size for table rows */
    }
    #hoa, #par {
      width: 15%; /* Adjust the width as needed */
      word-wrap: break-word; /* Ensure long words wrap */
      word-break: break-word; /* Prevent overflow */
    }
    #cq{
      width: 8%; /* Adjust the width as needed */
      word-wrap: break-word; /* Ensure long words wrap */
      word-break: break-word; /* Prevent overflow */
    }
    #pm{
      width: 12%; /* Adjust the width as needed */
      word-wrap: break-word; /* Ensure long words wrap */
      word-break: break-word; /* Prevent overflow */
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
    thead th {
        background-color: #214597 !important;
        color: #fff !important;
        -webkit-print-color-adjust: exact; /* Chrome, Safari */
        print-color-adjust: exact; /* Firefox */
    }
    tr {
        background-color: #214597 !important;
    }

    .text-right {
                text-align: right !important;
            }
            h2 {
    
    font-size: 13px !important;
            }
}
</style>
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
        <h5 style="text-align:right">{{ $extra['voucher_type'] }}</h5>
        <h2>
        {{ config('settings.company_name') }} ({{ config('settings.address_1') }})</h2>
        <p>  <strong>Showing For:</strong>  @if($search_by['from']!='' &&  $search_by['to']!='')
           {{$search_by['from']}} -{{$search_by['to']}}
            @else
             All Time
            @endif</p>
          <p>  <strong>Date:</strong>  {{ date('d M Y') }}<br>
           
        </p>
 
        <button type="button" onclick="window.print()" class="btn btn-primary" id="print_button" 
            style="background-color: #007bff; border: none; border-radius: 5px; 
                   padding: 10px 20px; float: right;">
        <i class="fas fa-print" style="margin-right: 8px;"></i>Print
    </button>

       
    </div>

    
    
    <div class="mid">
        <?php $total_dr = 0; ?>
        <?php $total_cr = 0;
        $branch_number = 1;
        ?>
        @foreach ($items['branches'] as $branch_name => $income_expenses)
            <div class="row">
                <div class="col-md-12 col-sm-12 col-lg-12 col-xl-12">
                <h4 class="text-center mb-3">   {{ $branch_name }}</h4>
                    <table class="table table-bordered table-sm table-hover">
                        <thead>
                           
                            <tr id="trtop">
                                <th class="text-center"  style="background-color: #214597 !important; color: #fff;">
                                    Sl. No
                                </th>
                                <th class="text-center font-s-18"  style="background-color: #214597 !important; color: #fff;">
                                    Head Of Account
                                </th>
                                <th class="text-center font-s-18" colspan="2"  style="background-color: #214597 !important; color: #fff;">
                                    @if (!empty($search_by['from']))
                                        From {{ date(config('settings.date_format'), strtotime($search_by['from'])) }} to
                                        {{ date(config('settings.date_format'), strtotime($search_by['to'])) }}
                                    @else
                                        UpTo to {{ $extra['current_date_time'] }}
                                    @endif
                                </th>
                            </tr>
                            </thead>
                            <tr style="background-color:#cfd3d7 !important;text-align:center;color:red">
                                <th class="text-center" scope="col"style="background-color:#cfd3d7 !important;text-align:center;color:red"></th>
                                <th scope="col"style="background-color:#cfd3d7 !important;text-align:center;color:red"></th>
                                <th class="text-right" scope="col"style="background-color:#cfd3d7 !important;text-align:center;color:red">Dr ( <?php echo config('settings.is_code') == 'code' ? config('settings.currency_code') : config('settings.currency_symbol'); ?> )
                                </th>
                                <th class="text-right" scope="col"style="background-color:#cfd3d7 !important;text-align:center;color:red">Cr ( <?php echo config('settings.is_code') == 'code' ? config('settings.currency_code') : config('settings.currency_symbol'); ?> )
                                </th>
                            </tr>
                        
                        <tbody>
                            <?php $sub_dr = 0; ?>
                            <?php $sub_cr = 0; ?>
                            @php
                                $key = 0;
                            @endphp
                            @foreach ($income_expenses->whereNotNull('income_expense_head_id')->groupBy('IncomeExpenseHead.name') as $income_expense_name => $transactions)
                                @php
                                    $balance = 0;
                                @endphp
                                @foreach ($transactions as $transaction)
                                    @if ($transaction->IncomeExpenseHead->type == 1)
                                        @php
                                            $balance += $transaction->dr - $transaction->cr;
                                        @endphp
                                    @else
                                        @php
                                            $balance += $transaction->cr - $transaction->dr;
                                        @endphp
                                    @endif
                                @endforeach
                                <tr id="trtop">
                                    <th class="text-center" scope="row">{{ $key + 1 }}</th>
                                    <td scope="row">{{ $income_expense_name }}</td>
                                    <td class="text-right">
                                        @if ($transaction->IncomeExpenseHead->type == 1)
                                            @php
                                                $sub_dr += $balance;
                                            @endphp
                                            {{ Helper::convertMoneyFormat($balance) }}
                                        @else
                                            0
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        @if ($transaction->IncomeExpenseHead->type == 0)
                                            @php
                                                $sub_cr += $balance;
                                            @endphp
                                            {{ Helper::convertMoneyFormat($balance) }}
                                        @else
                                            0
                                        @endif
                                    </td>
                                </tr>
                                @php
                                    $key++;
                                @endphp
                            @endforeach
                            <?php $total_dr += $sub_dr; ?>
                            <?php $total_cr += $sub_cr; ?>
                            <tr>
                                <th class="text-right" colspan="2">Sub Total =</th>
                                <th class="text-right">{{ Helper::convertMoneyFormat($sub_dr) }}</th>
                                <th class="text-right">{{ Helper::convertMoneyFormat($sub_cr) }}</th>
                            </tr>
                            @if (count($items['branches']) == $branch_number)
                                <tr style="background-color:#bdb8b8;color:#fff">
                                    <th class="text-right font-s-20" colspan="2" style="background-color: #bdb8b8 !important; color: #ec2026 !important ;">Total Amount=</th>
                                    <th class="text-right font-s-20"style="background-color: #bdb8b8 !important; color: #ec2026 !important ;">{{ Helper::convertMoneyFormat($total_dr) }}
                                    </th>
                                    <th class="text-right font-s-20" style="background-color: #bdb8b8 !important; color: #ec2026 !important ;">{{ Helper::convertMoneyFormat($total_cr) }}
                                    </th>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            <br>
            <?php $branch_number++; ?>
        @endforeach
        <div class="row">
            <div class="col-md-12 col-sm-12 col-lg-12 col-xl-12">
                <table class="table table-bordered table-sm table-hover">
                    <thead>
                        <tr id="trtop">
                            <th class="text-center"  style="background-color: #214597 !important; color: #fff;">
                                Sl. No
                            </th>
                            <th class="text-center font-s-18"  style="background-color: #214597 !important; color: #fff;">
                                Closing Bank & Cash Balance
                            </th>
                            <th class="text-center font-s-18" colspan="2"  style="background-color: #214597 !important; color: #fff;">
                                @if (!empty($search_by['from']))
                                    From {{ date(config('settings.date_format'), strtotime($search_by['from'])) }} to
                                    {{ date(config('settings.date_format'), strtotime($search_by['to'])) }}
                                @else
                                    UpTo to {{ $extra['current_date_time'] }}
                                @endif
                            </th>
                        </tr>
                        </thead>
                        <tr style="background-color:#cfd3d7 !important;color:#fff;text-align:center;color:red">
                            <th class="text-center" scope="col" style="background-color:#cfd3d7 !important;text-align:center;color:red"></th>
                            <th scope="col"style="background-color:#cfd3d7 !important;text-align:center;color:red"></th>
                            <th class="text-right" scope="col"style="background-color:#cfd3d7 !important;text-align:center;color:red">Dr ( <?php echo config('settings.is_code') == 'code' ? config('settings.currency_code') : config('settings.currency_symbol'); ?> )
                            </th>
                            <th class="text-right" scope="col"style="background-color:#cfd3d7 !important;text-align:center;color:red">Cr ( <?php echo config('settings.is_code') == 'code' ? config('settings.currency_code') : config('settings.currency_symbol'); ?> )
                            </th>
                        </tr>
                   
                    <tbody>
                        @php
                            $total_bank_cash_balance = 0;
                            $key = 1;
                        @endphp
                        @foreach ($items['bank_cashes'] as $bank_cashes_name => $transactions)
                            @php
                                $bank_dr = 0;
                                $bank_cr = 0;
                            @endphp
                            @foreach ($transactions as $transaction)
                                @php
                                    $bank_dr += (int) $transaction->dr;
                                    $bank_cr += (int) $transaction->cr;
                                @endphp
                            @endforeach
                            <tr>
                                <th class="text-center" scope="row">{{ $key }}</th>
                                <td scope="row">{{ $bank_cashes_name }}</td>
                                <td class="text-right">
                                    @php
                                        $total_bank_cash_balance += $sub_bank_cash_balance = $bank_cr - $bank_dr;
                                    @endphp
                                    {{ Helper::convertMoneyFormat($sub_bank_cash_balance) }}
                                </td>
                                <td class="text-right">
                                    0
                                </td>
                            </tr>
                            @php
                                $key++;
                            @endphp
                        @endforeach
                        <tr >
                            <th class="text-right" colspan="2">Sub Total =</th>
                            <th class="text-right">{{ Helper::convertMoneyFormat($total_bank_cash_balance) }}</th>
                            <th class="text-right">{{ Helper::convertMoneyFormat(0) }}</th>
                        </tr>
                        <tr style="background-color:#bdb8b8;color:#fff">
                            <th class="text-right font-s-20" colspan="2"style="background-color: #bdb8b8 !important; color: #ec2026 !important ;">Grand Total Amount =</th>
                            <th class="text-right font-s-20"style="background-color: #bdb8b8 !important; color: #ec2026 !important ;">{{ Helper::convertMoneyFormat($total_cr) }}</th>
                            <th class="text-right font-s-20"style="background-color: #bdb8b8 !important; color: #ec2026 !important ;">
                                {{ Helper::convertMoneyFormat($total_dr + $total_bank_cash_balance) }}</th>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <br>
    </div>
@stop
