@extends('layouts.pdf')
@section('title')
    {{ config('settings.company_name') }} -> {{ $extra['module_name'] }}
@endsection
@section('content')
    <div class="mid">
        <h2 class="text-center">{{ config('settings.company_name') }}</h2>
        <h5 class="text-center ">{{ config('settings.address_1') }}</h5>
        <hr>
        <h4 class="text-center mb-4">{{ $extra['voucher_type'] }}</h4>
    </div>
    <div class="mid mb-3">
        <div class="row">
            <div class="col-md-12 col-sm-12 col-lg-12 col-xl-12">
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <td class="text-right">Search By:</td>
                            <td class="text-right">Branch Name:</td>
                            <th>{{ $search_by['branch_name'] }}</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    <div class="mid">
        <div class="row">
            <div class="col-md-12 col-sm-12 col-lg-12 col-xl-12">
                <table class="table table-bordered table-sm table-hover">
                    <thead>
                        <tr>
                            <th class="text-center">
                                <h5>Particulars </h5>
                            </th>
                            <th class="text-center">
                                @if ($search_by['start_from'])
                                    <h5>From {{ $search_by['start_from'] }} To {{ $search_by['start_to'] }}</h5>
                                @endif
                            </th>
                            <th class="text-center">
                                @if ($search_by['end_from'])
                                    <h5>From {{ $search_by['end_from'] }} To {{ $search_by['end_to'] }}</h5>
                                @endif
                            </th>
                        </tr>
                        <tr>
                            <th class="text-center" scope="col"></th>
                            <th class="text-right" scope="col"> <?php echo config('settings.is_code') == 'code' ? config('settings.currency_code') : config('settings.currency_symbol'); ?>
                            </th>
                            <th class="text-right" scope="col"> <?php echo config('settings.is_code') == 'code' ? config('settings.currency_code') : config('settings.currency_symbol'); ?>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($particulars as $key => $particular)
                            <tr
                                @if ($key == 'Revenue' or
                                    $key == 'GrossProfit' or
                                    $key == 'IncomeFromOperation' or
                                    $key == 'IncomeBeforeTaxAndInterest' or
                                    $key == 'IncomeAfterTaxAndInterest') class="font-s-16 font-w-b"
                            @elseif ($key == 'NetProfitOrLoss')
                            class="font-s-20 font-w-b" @endif>
                                <td class="text-left" scope="row">{{ $particular['name'] }}</td>
                                <td scope="row" class="text-right">
                                    {{ Helper::convertMoneyFormat($particular['balance']['start_balance']) }} </td>
                                <td class="text-right">
                                    {{ Helper::convertMoneyFormat($particular['balance']['end_balance']) }} </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <br>
    </div>
@stop
