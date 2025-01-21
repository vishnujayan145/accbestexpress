@extends('layouts.pdf')
@section('title')
    {{ $extra['module_name'] }}
@endsection
@section('content')
    <div class="mid">
        <div class="row">
            <div class="col-md-12 col-sm-12 col-lg-12 col-xl-12">
                <table class="table table-bordered table-sm table-hover">
                    <thead>
                        <tr>
                            <th class="text-center">
                                Sl.No
                            </th>
                            <th class="text-center">Voucher No</th>
                            <th class="text-center">Type</th>
                            <th>Particulars</th>
                            <th>Branch Name</th>
                            <th>Date</th>
                            <th>Ledger Name</th>
                            <th>Made Of Payment</th>
                            <th>CHQ. No</th>
                            <th>Debit ( <?php echo config('settings.is_code') == 'code' ? config('settings.currency_code') : config('settings.currency_symbol'); ?>
                                )
                            </th>
                            <th>Credit ( <?php echo config('settings.is_code') == 'code' ? config('settings.currency_code') : config('settings.currency_symbol'); ?>
                                )
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $sl = 1;
                        @endphp
                        @foreach ($items as $item)
                            <tr>
                                <td class="text-center">{{ $sl }}</td>
                                <td class="text-center">{{ str_pad($item->voucher_no, 4, '0', STR_PAD_LEFT) }}</td>
                                <td>{{ $item->voucher_type }}</td>
                                <td>{{ $item->particulars }}</td>
                                <td>{{ $item->Branch ? $item->Branch->name : '' }}</td>
                                <td>{{ date(config('settings.date_format'), strtotime($item->voucher_date)) }}</td>
                                <td>
                                    {{ $item->IncomeExpenseHead ? $item->IncomeExpenseHead->name : '' }}
                                </td>
                                <td>
                                    {{ $item->BankCash ? $item->BankCash->name : '' }}
                                </td>
                                <td> {{ $item->cheque_number }} </td>
                                <td>{{ $item->dr }}</td>
                                <td>{{ $item->cr }}</td>
                            </tr>
                            @php
                                $sl++;
                            @endphp
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop
