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

    h4,
    h5 {
        font-weight: 600;
        margin: 5px 0;
        color: #ec2026;
        margin-right: 20px !important;
    }

    /* Table Styling */
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }

    th,
    td {
        border: 1px solid #999;
        padding: 8px;
    }

    thead th {
        background-color: #214597;
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

    #hoa,
    #par {
        width: 20%;
        /* Adjust the width as needed */
        word-wrap: break-word;
        /* Ensure long words wrap */
        word-break: break-word;
        /* Prevent overflow */
    }

    .table-sm td,
    .table-sm th {
        padding: 0.5rem;
    }

    .text-right h3,
    h2,
    p {
        margin-right: 20px !important;
    }

    thead th {
        background-color: #214597;
        color: #fff;
    }


    img {
        max-width: 100%;
        /* Ensure images are scaled properly */
    }

    #print_button button {
        position: fixed;
        top: 20px;
        right: 1050px;
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
        text-align: right;
    }

    h1 {
        color: #ec2026 !important;
        text-align: right;
        margin-right: 20px;

    }

    p {
        color: #555;
        font-size: 16px;
        line-height: 1.5;
        text-align: right;

    }

    #print_button {
        margin-top: 10px;
        margin-right: 20px;
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

    .col-md.6 {
        padding-right: 0px !important;
    }

    @media (max-width: 767.98px) {

        .col-md-6 {
            width: 50% !important;
        }

        .col-md-6 {
            float: left !important;
        }

        h2 {
            color: #214597 !important;
            font-weight: bold;
            font-size: 14px !important;
            margin-bottom: 10px;
        }

        h1 {
            color: #ec2026 !important;
            font-size: 25px !important;
        }

        .col-md.6 {
            padding-right: 0px !important;
        }
    }

    @media print {
        @page {
            size: auto;
            /* Adjust to fit the content */
            margin: 0;
            /* No margin */
        }

        .col-md.6 {
            padding-right: 0px !important;
        }

        h2,
        h5,
        p {
            margin-right: 0px !important;
        }

        /* You can target specific rows if necessary */
        #trtop {
            font-size: 10px !important;
            /* Smaller font size for table rows */
        }

        #hoa,
        #par {
            width: 15%;
            /* Adjust the width as needed */
            word-wrap: break-word;
            /* Ensure long words wrap */
            word-break: break-word;
            /* Prevent overflow */
        }

        #cq {
            width: 8%;
            /* Adjust the width as needed */
            word-wrap: break-word;
            /* Ensure long words wrap */
            word-break: break-word;
            /* Prevent overflow */
        }

        #pm {
            width: 12%;
            /* Adjust the width as needed */
            word-wrap: break-word;
            /* Ensure long words wrap */
            word-break: break-word;
            /* Prevent overflow */
        }

        body {
            margin: 0;
            /* Removes body margin */
            padding: 0;
            /* Removes body padding */
        }

        .container {
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        #print_button,
        .mt-3,
        .table3 {
            display: none;
        }

        thead th {
            background-color: #214597 !important;
            color: #fff !important;
            -webkit-print-color-adjust: exact;
            /* Chrome, Safari */
            print-color-adjust: exact;
            /* Firefox */
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

                        <div class="col-md-6">
                            <!-- Agency Name and Address (to be updated dynamically) -->
                            <h5 style="text-align:right">{{ $extra['voucher_type'] }}</h5>
                            <h2>
                                {{ config('settings.company_name') }} ({{ config('settings.address_1') }})
                            </h2>
                            <p> <strong>Showing For:</strong> @if($search_by['from']!='' && $search_by['to']!='')
                                {{$search_by['from']}} -{{$search_by['to']}}
                                @else
                                All Time
                                @endif
                            </p>
                            <p> <strong>Date:</strong> {{ date('d M Y') }}<br>

                            </p>

                            <button type="button" onclick="window.print()" class="btn btn-primary" id="print_button"
                                style="background-color: #007bff; border: none; border-radius: 5px; 
                                padding: 10px 20px; float: right;">
                                <i class="fas fa-print" style="margin-right: 8px;"></i>Print
                            </button>


                        </div>


                        <div class="mid">
                            @foreach ($items as $item)

                            @if ($item->Transactions->count() > 0 || $shipments->count()>0)
                            <div class="row">
                                <div class="col-md-12 col-sm-12 col-lg-12 col-xl-12">
                                    <h4 class="text-center mb-3">{{ $item->name }}</h4>
                                    <table class="table table-bordered table-sm table-hover">
                                        <thead>
                                            <tr id="trtop">
                                                <th class="text-center" scope="col" style="background-color: #214597 !important; color: #fff;">Sl.No</th>
                                                <th scope="col" style="background-color: #214597 !important; color: #fff;">Date</th>
                                                <th scope="col" style="background-color: #214597 !important; color: #fff;">Branch Name</th>

                                                <th scope="col" style="background-color: #214597 !important; color: #fff;">Reference No</th>
                                                <th scope="col" style="background-color: #214597 !important; color: #fff;">Particulars</th>
                                                <th scope="col" style="background-color: #214597 !important; color: #fff;">Voucher No</th>
                                                <th scope="col" style="background-color: #214597 !important; color: #fff;">Voucher Type</th>
                                                <th class="text-right" scope="col" style="background-color: #214597 !important; color: #fff;">Dr ( <?php echo config('settings.is_code') == 'code' ? config('settings.currency_code') : config('settings.currency_symbol'); ?> )
                                                </th>
                                                <th class="text-right" scope="col" style="background-color: #214597 !important; color: #fff;">Cr ( <?php echo config('settings.is_code') == 'code' ? config('settings.currency_code') : config('settings.currency_symbol'); ?> )
                                                </th>
                                                <th class="text-right" scope="col" style="background-color: #214597 !important; color: #fff;">Balance
                                                    ( <?php echo config('settings.is_code') == 'code' ? config('settings.currency_code') : config('settings.currency_symbol'); ?> )
                                                </th>
                                            </tr>
                                        </thead>
                                        <tr style="background-color:#cfd3d7 !important;color:#fff">
                                            <th colspan="7" style="text-align:center;color:red">Opening Balance</th>
                                            @php
                                            $type = $search_by['opening_balance'];
                                            @endphp
                                            @if ($type < 0)
                                                <th style="text-align:right;color:red">{{ $search_by['opening_balance'] }}</th>
                                                <th></th>
                                                @else
                                                <th></th>
                                                <th style="text-align:right;color:red">{{ $search_by['opening_balance'] }}</th>
                                                @endif
                                                <th style="text-align:right;color:red">
                                                    @if ($type == 1)
                                                    -{{ $search_by['opening_balance'] }}
                                                    @else
                                                    {{ $search_by['opening_balance'] }}
                                                    @endif
                                                </th>
                                        </tr>

                                        <tbody>
                                            @php
                                            $dr_sum=0;
                                            $cr_sum=0;
                                            $dr_balance = 0;
                                            $cr_balance = 0;
                                            $op_balance = $search_by['opening_balance'];
                                            $balance = ($type == 1) ? -$op_balance : $op_balance;
                                            @endphp
                                            @if(isset($item->Transactions) && $item->Transactions->isNotEmpty())

                                            @foreach ($item->Transactions as $key => $transaction)
                                            @if ($item->type)
                                            <?php $balance += - ($transaction->dr - $transaction->cr); ?>
                                            @else
                                            <?php $balance += $transaction->cr - $transaction->dr; ?>
                                            @endif
                                            @php

                                            if ($transaction->voucher_type == 'CV') {
                                            $ParentRouteName = 'cr_voucher';
                                            } elseif ($transaction->voucher_type == 'JV') {
                                            $ParentRouteName = 'jnl_voucher';
                                            } elseif ($transaction->voucher_type == 'Contra') {
                                            $ParentRouteName = 'contra_voucher';
                                            }
                                            else {
                                            $ParentRouteName = 'dr_voucher';
                                            }

                                            @endphp
                                            <tr id="trtop" onclick="window.location='{{ route($ParentRouteName.'.edit', $transaction->voucher_no) }}';" style="cursor:pointer;">
                                                <th class="text-center" scope="row">{{ $key+1 }}</th>
                                                <td>{{ date(config('settings.date_format'), strtotime($transaction->voucher_date)) }}</td>
                                                <td>{{ $transaction->Branch ? $transaction->Branch->name : '' }}</td>
                                                <td id="cq">{{ $transaction->reference_no }}</td>
                                                <td id="par">{{ $transaction->particulars }}</td>
                                                <td class="text-center">{{ $transaction->voucher_no }}</td>
                                                <td>{{ $transaction->voucher_type }}</td>
                                                <td class="text-right">{{ $transaction->dr }}</td>
                                                <td class="text-right">{{ $transaction->cr }}</td>
                                                <td class="text-right">{{ $balance }}</td>
                                            </tr>
                                            @php
                                            $dr_balance += $transaction->dr;
                                            $cr_balance += $transaction->cr;
                                            @endphp
                                            <?php

                                            $dr_sum = $dr_sum + $transaction->dr;
                                            $cr_sum = $cr_sum + $transaction->cr;
                                            ?>
                                            @endforeach
                                            @else
                                            @php
                                            $key=-1;
                                            @endphp
                                            @endif
                                            @foreach ($shipments as $index => $shipment)
                                            @php
                                            $balance= $balance+ $shipment->paid;
                                            $cr_sum=$cr_sum+$shipment->paid;
                                            @endphp
                                            <tr>
                                                <th class="text-center" scope="row">{{ $key + $index + 2  }}</th>
                                                <td>{{ date(config('settings.date_format'), strtotime($shipment->created_at)) }}</td>
                                                <td>{{ $shipment->name }}</td>
                                                <td></td>
                                                <td>Invoice payment - {{strtoupper($shipment->sender)}}</td>
                                                <td>{{ $shipment->booking_number }}</td>
                                                <td>Invoice </td>
                                                <td> </td>
                                                <td class="text-right">{{ $shipment->paid }} </td>

                                                <td class="text-right">{{$balance}}</td> {{-- No balance calculation --}}
                                            </tr>
                                            @endforeach

                                            <tr style="background-color:#6c757d;color:#fff">
                                                <th class="text-right" colspan="7" style="background-color: #bdb8b8 !important; color: #ec2026 !important ;">Total =</th>

                                                {{-- Total Dr --}}
                                                <th class="text-right" style="background-color: #bdb8b8 !important; color: #ec2026 !important ;">
                                                    @if ($type < 0)
                                                        {{ number_format($dr_sum + abs($type), 2) }}
                                                        @else
                                                        {{ number_format($dr_sum, 2) }}
                                                        @endif
                                                        </th>

                                                        {{-- Total Cr --}}
                                                <th class="text-right" style="background-color: #bdb8b8 !important; color: #ec2026 !important ;">
                                                    @if ($type > 0)
                                                    {{ number_format($cr_sum + abs($type), 2) }}
                                                    @else
                                                    {{ number_format($cr_sum, 2) }}
                                                    @endif
                                                </th>

                                                {{-- Final Balance --}}
                                                <th class="text-right" style="background-color: #bdb8b8 !important; color: #ec2026 !important ;">{{ number_format($balance, 2) }}</th>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <br>
                            @else
                            <div class="row">
                                <div class="col-md-12 col-sm-12 col-lg-12 col-xl-12">
                                    <h4 class="text-center mb-3">{{ $item->name }}</h4>
                                    <table class="table table-bordered table-sm table-hover">
                                        <thead>
                                            <tr id="trtop">
                                                <th class="text-center" scope="col" style="background-color: #214597 !important; color: #fff;">Sl.No</th>
                                                <th scope="col" style="background-color: #214597 !important; color: #fff;">Date</th>
                                                <th scope="col" style="background-color: #214597 !important; color: #fff;">Branch Name</th>

                                                <th scope="col" style="background-color: #214597 !important; color: #fff;">Reference No</th>
                                                <th scope="col" style="background-color: #214597 !important; color: #fff;">Particulars</th>
                                                <th scope="col" style="background-color: #214597 !important; color: #fff;">Voucher No</th>
                                                <th scope="col" style="background-color: #214597 !important; color: #fff;">Voucher Type</th>
                                                <th class="text-right" scope="col" style="background-color: #214597 !important; color: #fff;">Dr ( <?php echo config('settings.is_code') == 'code' ? config('settings.currency_code') : config('settings.currency_symbol'); ?> )
                                                </th>
                                                <th class="text-right" scope="col" style="background-color: #214597 !important; color: #fff;">Cr ( <?php echo config('settings.is_code') == 'code' ? config('settings.currency_code') : config('settings.currency_symbol'); ?> )
                                                </th>
                                                <th class="text-right" scope="col" style="background-color: #214597 !important; color: #fff;">Balance
                                                    ( <?php echo config('settings.is_code') == 'code' ? config('settings.currency_code') : config('settings.currency_symbol'); ?> )
                                                </th>
                                            </tr>

                                        </thead>
                                        <tr style="background-color:#cfd3d7 !important;color:#fff;text-align:center;color:red">
                                            <th colspan="7">Opening Balance</th>
                                            @php
                                            $type = $search_by['opening_balance'];
                                            @endphp
                                            @if ($type < 0)
                                                <th style="text-align:right;color:red">{{ $search_by['opening_balance'] }}</th>
                                                <th></th>
                                                @else
                                                <th></th>
                                                <th style="text-align:right;color:red">{{ $search_by['opening_balance'] }}</th>
                                                @endif
                                                <th style="text-align:right;color:red">
                                                    @if ($type == 1)
                                                    -{{ $search_by['opening_balance'] }}
                                                    @else
                                                    {{ $search_by['opening_balance'] }}
                                                    @endif
                                                </th>
                                        </tr>

                                        <tbody>
                                            <tr>
                                                <th style="color:Blue" class="text-center" colspan="10">No transactions found for this date range.</th>

                                            </tr>
                                            <tr style="background-color:#6c757d;color:#fff">
                                                <th class="text-right" colspan="7" style="background-color: #bdb8b8 !important; color: #ec2026 !important ;">Total =</th>

                                                {{-- Total Dr --}}
                                                <th class="text-right" style="background-color: #bdb8b8 !important; color: #ec2026 !important ;">
                                                    @if ($type < 0)
                                                        {{ number_format(abs($type), 2) }}
                                                        @else
                                                        {{ number_format(0, 2) }}
                                                        @endif
                                                        </th>

                                                        {{-- Total Cr --}}
                                                <th class="text-right" style="background-color: #bdb8b8 !important; color: #ec2026 !important ;">
                                                    @if ($type > 0)
                                                    {{ number_format(abs($type), 2) }}
                                                    @else
                                                    {{ number_format(0, 2) }}
                                                    @endif
                                                </th>

                                                {{-- Final Balance --}}
                                                <th class="text-right" style="background-color: #bdb8b8 !important; color: #ec2026 !important ;">{{ number_format($search_by['opening_balance'] , 2) }}</th>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <br>

                                </div>
                            </div>
                            @endif
                            @endforeach
                        </div>
                        @stop