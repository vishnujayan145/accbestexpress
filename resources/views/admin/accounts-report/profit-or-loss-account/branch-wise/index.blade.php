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
                    <p>  <strong>Showing For:</strong> All Time</p>
                      
                    <p>  <strong>Date:</strong>  {{ date('d M Y') }}<br>
                    
                    </p>
 
                        <button type="button" onclick="window.print()" class="btn btn-primary" id="print_button" 
                            style="background-color: #007bff; border: none; border-radius: 5px; 
                                padding: 10px 20px; float: right;">
                        <i class="fas fa-print" style="margin-right: 8px;"></i>Print
                    </button>
             </div>
             @php
                use Illuminate\Support\Str;
            @endphp
          
            <div class="mid">
    <!-- Display table header only once -->
    <table class="table table-bordered table-sm table-hover">
        <thead>
            <tr id="trtop">
                <th class="text-center" scope="col" style="background-color: #214597!important; color: #fff;">Sl.No</th>
                <th scope="col" style="background-color: #214597 !important; color: #fff;">Particulars</th>
                <th scope="col" style="background-color: #214597 !important; color: #fff;">Ledger Type</th>
                <th class="text-right" scope="col" style="background-color: #214597 !important; color: #fff;">
                    Debit ({{ config('settings.is_code') == 'code' ? config('settings.currency_code') : config('settings.currency_symbol') }})
                </th>
                <th class="text-right" scope="col" style="background-color: #214597 !important; color: #fff;">
                    Credit ({{ config('settings.is_code') == 'code' ? config('settings.currency_code') : config('settings.currency_symbol') }})
                </th>
            </tr>
        </thead>
        <tbody>

<!-- Income Section -->
<tr style="cursor:pointer; color:blue;">
    <th colspan="4">INCOME</th>
    <th class="text-right">{{ $transactions->where('cr', '>', 0)->sum('cr') }}</th>
</tr>

@php
    $incomeGroups = $transactions->where('cr', '>', 0)->groupBy('incomeExpenseHead.incomeExpenseType.name');
  
@endphp

@foreach ($incomeGroups as $ledgerType => $incomeTransactions)
    <!-- Ledger Type Header for Income with Sum -->
    <tr id="ledger-type-{{ Str::slug($ledgerType) }}" class="ledger-type" style="cursor:pointer;color:green" onclick="toggleRows('{{ Str::slug($ledgerType) }}')">
        <th colspan="4">{{ $ledgerType }}</th>
        <th class="text-right">{{ $incomeTransactions->sum('cr') }}</th>
    </tr>
    @php $slNo = 1;@endphp
    <!-- Transaction rows (initially hidden) -->
    @foreach ($incomeTransactions as $transaction)
        <tr class="transaction-row ledger-{{ Str::slug($ledgerType) }}" style="display: none;">
            <th class="text-center" scope="row">{{ $slNo++ }}</th>
            <td id="par">{{ $transaction->incomeExpenseHead->name ?? 'N/A' }}</td>
            <td>{{ $transaction->incomeExpenseHead->incomeExpenseType->name ?? 'N/A' }}</td>
            <td class="text-right"></td>
            <td class="text-right">{{ $transaction->cr }}</td>
        </tr>
    @endforeach
@endforeach

<!-- Expense Section -->
<tr style="cursor:pointer; color:blue;">
    <th colspan="3">EXPENSE</th>
    <th class="text-right">{{ $transactions->where('dr', '>', 0)->sum('dr') }}</th>
    <th></th>
</tr>

@php
    $expenseGroups = $transactions->where('dr', '>', 0)->groupBy('incomeExpenseHead.incomeExpenseType.name');
   
@endphp

@foreach ($expenseGroups as $ledgerType => $expenseTransactions)
    <!-- Ledger Type Header for Expense with Sum -->
    <tr id="ledger-type-{{ Str::slug($ledgerType) }}" class="ledger-type" style="cursor:pointer;color:green" onclick="toggleRows2('{{ Str::slug($ledgerType) }}')">
        <th colspan="3">{{ $ledgerType }}</th>
        <th class="text-right">{{ $expenseTransactions->sum('dr') }}</th>
        <th></th>
    </tr>
    @php $slNo = 1;@endphp
    <!-- Transaction rows (initially hidden) -->
    @foreach ($expenseTransactions as $transaction)
        <tr class="transaction-row eledger-{{ Str::slug($ledgerType) }}" style="display: none;">
            <th class="text-center" scope="row">{{ $slNo++ }}</th>
            <td id="par">{{ $transaction->incomeExpenseHead->name ?? 'N/A' }}</td>
            <td>{{ $transaction->incomeExpenseHead->incomeExpenseType->name ?? 'N/A' }}</td>
            <td class="text-right">{{ $transaction->dr }}</td>
            <td class="text-right"></td>
        </tr>
    @endforeach
@endforeach

</tbody>
    </table>
</div>



@stop
<script>
    function toggleRows2(ledgerType) {
        // Toggle visibility of rows with the specified ledger type
        const rows = document.querySelectorAll(`.eledger-${ledgerType}`);
        rows.forEach(row => {
            row.style.display = (row.style.display === 'none') ? 'table-row' : 'none';
        });

       
    }
    function toggleRows(ledgerType) {
        // Toggle visibility of rows with the specified ledger type
        const rows = document.querySelectorAll(`.ledger-${ledgerType}`);
        rows.forEach(row => {
            row.style.display = (row.style.display === 'none') ? 'table-row' : 'none';
        });

       
    }
</script>