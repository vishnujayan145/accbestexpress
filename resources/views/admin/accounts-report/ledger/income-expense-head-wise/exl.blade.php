@extends('layouts.pdf')
@section('title')
    {{ $extra['module_name'] }}
@endsection
@section('content')
    <div class="mid">
        @foreach ($items as $income_expense_head)
            @if ($income_expense_head->Transaction->count() > 0)
                <div class="row">
                    <div class="col-md-12 col-sm-12 col-lg-12 col-xl-12">
                        <h4 class="text-center mb-3">{{ $income_expense_head->name }}</h4>
                        <table class="table table-bordered table-sm table-hover">
                            <thead>
                                <tr>
                                    <th class="text-center" scope="col">SL. No</th>
                                    <th scope="col">Date</th>
                                    <th scope="col">Branch Name</th>
                                    <th scope="col">Mode Of Payment</th>
                                    <th scope="col">Reference Number</th>
                                    <th scope="col">Particulars</th>
                                    <th scope="col">Voucher No</th>
                                    <th scope="col">Type Of Voucher</th>
                                    <th class="text-right" scope="col">Dr ( <?php echo config('settings.is_code') == 'code' ? config('settings.currency_code') : config('settings.currency_symbol'); ?> )
                                    </th>
                                    <th class="text-right" scope="col">Cr ( <?php echo config('settings.is_code') == 'code' ? config('settings.currency_code') : config('settings.currency_symbol'); ?> )
                                    </th>
                                    <th class="text-right" scope="col">Balance
                                        ( <?php echo config('settings.is_code') == 'code' ? config('settings.currency_code') : config('settings.currency_symbol'); ?> )
                                    </th>
                                </tr>
                                <tr>
                                    <th class="text-right" colspan="8"style="text-align:center;color:red">Opening Balance</th>
                                    @php
                                        $type = $income_expense_head->type;
                                    @endphp

                                    @if ($type == 1)
                                        <th style="text-align:right;color:red">-{{ $income_expense_head->opening_balance }}</th>
                                        <th></th>
                                    @else
                                    <th></th>
                                        <th style="text-align:right;color:red">{{ $income_expense_head->opening_balance }}</th>
                                    @endif
                                    <th class="text-right"style="text-align:right;color:red">
                                    @if ($type == 1)
                                        -{{ $income_expense_head->opening_balance }}
                                        @else
                                        {{ $income_expense_head->opening_balance }}
                                        @endif
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                $op_balance = $income_expense_head->opening_balance;
                                    $balance=($type == 1) ? -$op_balance : $op_balance;
                                   
                                @endphp
                                @foreach ($income_expense_head->Transaction as $key => $transaction)
                                    @if ($income_expense_head->type)
                                        {{-- Dr 1 Cr 0 --}}
                                        <?php $balance += -($transaction->dr - $transaction->cr); ?> {{-- Dr=Dr-Cr --}}
                                    @else
                                        <?php $balance += $transaction->cr - $transaction->dr; ?> {{-- Cr=Cr-Dr --}}
                                    @endif
                                    <tr>
                                        <th class="text-center" scope="row">{{ $key + 1 }}</th>
                                        <td>{{ date(config('settings.date_format'), strtotime($transaction->voucher_date)) }}
                                        </td>
                                        <td> {{ $transaction->Branch ? $transaction->Branch->name : '' }} </td>
                                        <td> {{ $transaction->BankCash ? $transaction->BankCash->name : '' }} </td>
                                        <td> {{ $transaction->reference_no }} </td>
                                        <td> {{ $transaction->particulars }} </td>
                                        <td class="text-center"> {{ $transaction->voucher_no }} </td>
                                        <td> {{ $transaction->voucher_type }} </td>
                                        <td class="text-right">{{ $transaction->dr }}
                                        </td>
                                        <td class="text-right">{{ $transaction->cr }}
                                        </td>
                                        <td class="text-right">
                                        
                                            {{ $balance }}
                                       
                                            
                                        </td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <th class="text-right" colspan="10">Total =</th>
                                   <th class="text-right">{{ $balance }}</th>
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
