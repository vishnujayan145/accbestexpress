@extends('layouts.pdf')

@section('title')
    {{ $extra['module_name'] }}
@endsection

@section('content')
    <div class="mid">
        <div class="row">
            @php
                $grand_total = 0;
            @endphp
            @foreach ($particulars as $key => $types)
                <div class="col-md-12 col-sm-12 col-lg-12 col-xl-12 mb-5">
                    <table class="table table-bordered table-sm table-hover">
                        <thead>
                            <tr>
                                <th class="text-center padding-t-8" colspan="2">
                                    <h3>{{ $key }}</h3>
                                </th>
                            </tr>
                            @if ($search_by['start_from'])
                                <tr>
                                    <th class="text-center">
                                    </th>
                                    <th class="text-center">
                                        <h5>From {{ $search_by['start_from'] }} To {{ $search_by['start_to'] }}</h5>
                                    </th>
                                </tr>
                            @endif
                            <tr>
                                <th class="text-left" scope="col">Ledger Name</th>
                                <th class="text-right" scope="col"> <?php echo config('settings.is_code') == 'code' ? config('settings.currency_code') : config('settings.currency_symbol'); ?>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $sub_total = 0;
                            @endphp
                            @foreach ($types->groupBy('IncomeExpenseHead.name') as $keyHead => $transactions)
                                @php
                                    $head_balance = 0;
                                @endphp
                                @foreach ($transactions as $transaction)
                                    @if ($transaction->IncomeExpenseHead->type)
                                        @php
                                            $head_balance += (int) $transaction->dr - (int) $transaction->cr;
                                        @endphp
                                    @else
                                        @php
                                            $head_balance += (int) $transaction->cr - (int) $transaction->dr;
                                        @endphp
                                    @endif
                                @endforeach
                                <tr>
                                    <td class="text-left" scope="row">{{ $keyHead }}</td>
                                    <td scope="row" class=" text-right">
                                        {{ Helper::convertMoneyFormat($head_balance) }}</td>
                                </tr>
                                @php
                                    $sub_total += $head_balance;
                                @endphp
                            @endforeach
                            <tr>
                                <th class="text-right"> Sub Total =</th>
                                <th class="text-right">{{ Helper::convertMoneyFormat($sub_total) }}</th>
                            </tr>
                            @php
                                $grand_total += $sub_total;
                            @endphp
                        </tbody>
                    </table>
                </div>
            @endforeach
            <div class="col-md-12 col-sm-12 col-lg-12 col-xl-12 mb-5">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th class="text-right">
                                <h2>Grand Total = </h2>
                            </th>
                            <th class="text-right">
                                <h2>{{ Helper::convertMoneyFormat($grand_total) }} </h2>
                            </th>
                        </tr>
                    </thead>
                </table>
            </div>

        </div>
        <br>
    </div>
@stop
