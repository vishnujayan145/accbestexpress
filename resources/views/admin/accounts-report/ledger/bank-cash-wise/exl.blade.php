@extends('layouts.pdf')
@section('title')
    {{ $extra['module_name'] }}
@endsection
@section('content')
    <div class="mid">
        @foreach ($items as $item)
            @if ($item->Transactions->count() > 0)
                <div class="row">
                    <div class="col-md-12 col-sm-12 col-lg-12 col-xl-12">
                        <h4 class="text-center mb-3">{{ $item->name }}</h4>
                        <table class="table table-bordered table-sm table-hover">
                            <thead>
                                <tr>
                                    <th class="text-center" scope="col">SL. No</th>
                                    <th scope="col">Date</th>
                                    <th scope="col">Branch Name</th>
                                    <th scope="col">Reference Number</th>
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
                                    $dr_balance = 0;
                                    $cr_balance = 0;
                                @endphp
                                @foreach ($item->Transactions as $key => $transaction)
                                    <tr>
                                        <th class="text-center" scope="row">{{ $key + 1 }}</th>
                                       <td>{{ date(config('settings.date_format'), strtotime($transaction->voucher_date)) }}</td>
                                        <td> {{ $transaction->Branch ? $transaction->Branch->name : '' }} </td>
                                        <td> {{ $transaction->reference_no }} </td>
                                        <td> {{ $transaction->particulars }} </td>
                                        <td class="text-center"> {{ $transaction->voucher_no }} </td>
                                        <td> {{ $transaction->voucher_type }} </td>
                                        <td class="text-right">{{ Helper::convertMoneyFormat($transaction->dr) }}</td>
                                        <td class="text-right">{{ Helper::convertMoneyFormat($transaction->cr) }}</td>
                                    </tr>
                                    @php
                                        $dr_balance += $transaction->dr;
                                        $cr_balance += $transaction->cr;
                                    @endphp
                                @endforeach
                                <tr>
                                    <th class="text-right" colspan="7">Total =</th>
                                    <th class="text-right">{{ Helper::convertMoneyFormat($dr_balance) }}</th>
                                    <th class="text-right">{{ Helper::convertMoneyFormat($cr_balance) }}</th>
                                </tr>
                                <tr>
                                    <th class="text-right" colspan="7">Balance =</th>
                                    <th class="text-left" colspan="2">
                                        {{ Helper::convertMoneyFormat($cr_balance - $dr_balance) }}</th>
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
