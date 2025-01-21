@extends('layouts.pdf')

@section('title')
    {{ $extra['module_name'] }}
@endsection
@section('content')
    <div class="mid">
        @php
            $total_dr = 0;
            $total_cr = 0;
            $branch_number = 1;
        @endphp
        @foreach ($items['branches'] as $branch_name => $income_expenses)
            <div class="row">
                <div class="col-md-12 col-sm-12 col-lg-12 col-xl-12">
                    <table class="table table-bordered table-sm table-hover">
                        <thead>
                            <tr>
                                <th colspan="4" class="text-center font-s-25">
                                    <b>{{ $branch_name }}</b>
                                </th>
                            </tr>
                            <tr>
                                <th class="text-center" scope="col"><b>Sl. No</b></th>
                                <th scope="col"><b>Head Of Account</b></th>
                                <th class="text-right" scope="col"><b>Dr
                                        ( <?php echo config('settings.is_code') == 'code' ? config('settings.currency_code') : config('settings.currency_symbol'); ?> )
                                    </b>
                                </th>
                                <th class="text-right" scope="col"><b>Cr
                                        ( <?php echo config('settings.is_code') == 'code' ? config('settings.currency_code') : config('settings.currency_symbol'); ?> )
                                    </b>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $sub_dr = 0;
                                $sub_cr = 0;
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
                                <tr>
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
                            @if ($items['branches']->count() == $branch_number)
                                <tr>
                                    <th class="text-right font-s-20" colspan="2">Total Amount=</th>
                                    <th class="text-right font-s-20">{{ Helper::convertMoneyFormat($total_dr) }}
                                    </th>
                                    <th class="text-right font-s-20">{{ Helper::convertMoneyFormat($total_cr) }}
                                    </th>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            <?php $branch_number++; ?>
        @endforeach
        <div class="row">
            <div class="col-md-12 col-sm-12 col-lg-12 col-xl-12">
                <table class="table table-bordered table-sm table-hover">
                    <thead>
                        <tr>
                            <th class="text-center font-s-18" colspan="4">
                                <b> Closing Bank &amp; Cash Balance</b>
                            </th>
                        </tr>
                        <tr>
                            <th class="text-center" scope="col"><b>Sl. No</b></th>
                            <th scope="col"></th>
                            <th class="text-right" scope="col"><b>Dr ( <?php echo config('settings.is_code') == 'code' ? config('settings.currency_code') : config('settings.currency_symbol'); ?> )
                                </b>
                            </th>
                            <th class="text-right" scope="col"><b>Cr ( <?php echo config('settings.is_code') == 'code' ? config('settings.currency_code') : config('settings.currency_symbol'); ?> )
                                </b>
                            </th>
                        </tr>
                    </thead>
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
                        <tr>
                            <th class="text-right" colspan="2">Sub Total =</th>
                            <th class="text-right">{{ Helper::convertMoneyFormat($total_bank_cash_balance) }}</th>
                            <th class="text-right">{{ Helper::convertMoneyFormat(0) }}</th>
                        </tr>
                        <tr>
                            <th class="text-right font-s-20" colspan="2">Grand Total Amount =</th>
                            <th class="text-right font-s-20">{{ Helper::convertMoneyFormat($total_cr) }}</th>
                            <th class="text-right font-s-20">
                                {{ Helper::convertMoneyFormat($total_dr + $total_bank_cash_balance) }}</th>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop
