@extends('layouts.pdf')
@push('include-css')
    <link rel="stylesheet" href="{{ asset('asset/css/main-report.css') }}">
@endpush
@section('title')
    {{ config('settings.company_name')  }} -> {{ $extra['module_name']  }}
@endsection
@section('content')
    <p class="mid">Printing Date & Time: {{ $extra['current_date_time']  }}</p>
    <div class="mid">
        <div class="col-md-12 col-sm-12 col-lg-12 col-xl-12">
            <br>
            <h2 class="text-center">{{ config('settings.company_name')  }}</h2>
            <h6 class="text-center">{{ config('settings.address_1')  }}</h6>
            <br>
            <h4 class="text-center">{{ $extra['voucher_type']  }}</h4>
            <hr>
        </div>
    </div>
    <div class="mid">
        <table class="table table-bordered table-sm">
            <thead>
            <tr>
                <td class="text-right">Search By:</td>
                <td class="text-right">Branch Name:</td>
                <td class="font-weight-bold">{{ $search_by['branch_name']  }}</td>
                <td class="text-right">Head Of Account:</td>
                <td class="font-weight-bold">{{ $search_by['income_expense_head_name']  }}</td>
                <td class="text-right">From Date:</td>
                <td class="font-weight-bold">{{ $search_by['from'] }}</td>
                <td class="text-right">To Date:</td>
                <td class="font-weight-bold">{{ $search_by['to'] }}</td>
            </tr>
            </thead>
        </table>
        @foreach ($items as $item)
            @if ($item->Transaction->count() > 0)
                <div class="row">
                    <div class="col-md-12 col-sm-12 col-lg-12 col-xl-12">
                        <h4 class="text-center mb-3">{{ $item->name }}</h4>
                        <table class="table table-bordered table-sm table-hover">
                            <thead>
                                <tr>
                                    <th class="text-center" scope="col">SL. No</th>
                                    <th scope="col">Date</th>
                                    <th scope="col">Head Of Account</th>
                                    <th scope="col">Mode Of Payment</th>
                                    <th scope="col">Cheque Number</th>
                                    <th scope="col">Particulars</th>
                                    <th scope="col">Voucher No</th>
                                    <th scope="col">Type Of Voucher</th>
                                    <th class="text-right" scope="col">Dr ( <?php echo config('settings.is_code') == 'code' ? config('settings.currency_code') : config('settings.currency_symbol'); ?> )
                                    </th>
                                    <th class="text-right" scope="col">Cr ( <?php echo config('settings.is_code') == 'code' ? config('settings.currency_code') : config('settings.currency_symbol'); ?> )
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $dr_amount = 0;
                                    $cr_amount = 0;
                                @endphp
                                @foreach ($item->Transaction as $key => $transaction)
                                    <tr>
                                        <th class="text-center" scope="row">{{ $key + 1 }}</th>
                                        <td>{{ date(config('settings.date_format'), strtotime($transaction->voucher_date)) }}
                                        </td>
                                        <td> {{ $transaction->IncomeExpenseHead ? $transaction->IncomeExpenseHead->name : '' }}
                                        </td>
                                        <td> {{ $transaction->BankCash ? $transaction->BankCash->name : '' }} </td>
                                        <td> {{ $transaction->cheque_number }} </td>
                                        <td> {{ $transaction->particulars }} </td>
                                        <td class="text-center"> {{ $transaction->voucher_no }} </td>
                                        <td> {{ $transaction->voucher_type }} </td>
                                        <td class="text-right">{{ Helper::convertMoneyFormat($transaction->dr) }}</td>
                                        <td class="text-right">{{ Helper::convertMoneyFormat($transaction->cr) }}</td>
                                    </tr>
                                    @php
                                        $dr_amount += $transaction->dr;
                                        $cr_amount += $transaction->cr;
                                    @endphp
                                @endforeach
                                <tr>
                                    <th class="text-right" colspan="8">Total =</th>
                                    <th class="text-right">{{ Helper::convertMoneyFormat($dr_amount) }}</th>
                                    <th class="text-right">{{ Helper::convertMoneyFormat($cr_amount) }}</th>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <br>
            @endif
        @endforeach
    </div>

@stop


