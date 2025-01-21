@extends('layouts.pdf')
@push('include-css')
    <link rel="stylesheet" href="{{ asset('asset/css/main-report.css') }}">
@endpush
@section('title')
    {{ config('settings.company_name')  }} -> {{ $extra['module_name']  }}
@endsection
@section('content')
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
    <div class="mid mb-3">
        <div class="row">
            <div class="col-md-12 col-sm-12 col-lg-12 col-xl-12">
                <table class="table table-bordered table-sm">
                    <thead>
                    <tr>
                        <td class="text-right">Search By:</td>
                        <td class="text-right">Branch Name:</td>
                        <th>{{ $search_by['branch_name']  }}</th>
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
                                @if ($search_by['start_from'] != null)
                                    <h5>From {{ $search_by['start_from'] }} To {{ $search_by['start_to'] }}</h5>
                                @endif
                            </th>
                            <th class="text-center">
                                @if ($search_by['end_from'] != null)
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
                        <tr>
                            <td class="text-left" scope="row">{{ $particulars['OpeningConstructionMaterial'] }}</td>
                            <td scope="row" class=" text-right">0</td>
                            <td class="text-right">0</td>
                        </tr>
                        <tr>
                            <td class="text-left" scope="row">
                                {{ $particulars['ConstructionMaterialPurchases']['from_amount']['name'] }}</td>
                            <td scope="row" class=" text-right">
                                {{ Helper::convertMoneyFormat($particulars['ConstructionMaterialPurchases']['from_amount']['value']) }}
                            </td>
                            <td class="text-right">
                                {{ Helper::convertMoneyFormat($particulars['ConstructionMaterialPurchases']['end_amount']['value']) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-left" scope="row">{{ $particulars['MaterialAvailableForUsed'] }}</td>
                            <td scope="row" class=" text-right">
                                {{ Helper::convertMoneyFormat($particulars['ConstructionMaterialPurchases']['from_amount']['value']) }}
                            </td>
                            <td class="text-right">
                                {{ Helper::convertMoneyFormat($particulars['ConstructionMaterialPurchases']['end_amount']['value']) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-left" scope="row">{{ $particulars['ClosingConstructionMaterial'] }}</td>
                            <td scope="row" class=" text-right">0</td>
                            <td class="text-right">0</td>
                        </tr>
                        <tr>
                            <th class="text-left" scope="row">{{ $particulars['MaterialUsedDuringThePeriod'] }}</th>
                            <th scope="row" class=" text-right">
                                {{ Helper::convertMoneyFormat($particulars['ConstructionMaterialPurchases']['from_amount']['value']) }}
                            </th>
                            <th class="text-right">
                                {{ Helper::convertMoneyFormat($particulars['ConstructionMaterialPurchases']['end_amount']['value']) }}
                            </th>
                        </tr>
                        <tr>
                            <td class="text-left" scope="row">
                                {{ $particulars['ConstructionLabourExpense']['from_amount']['name'] }}
                            </td>
                            <td scope="row" class="text-right">
                                {{ Helper::convertMoneyFormat($particulars['ConstructionLabourExpense']['from_amount']['value']) }}
                            </td>
                            <td scope="row" class="text-right">
                                {{ Helper::convertMoneyFormat($particulars['ConstructionLabourExpense']['end_amount']['value']) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-left" scope="row">
                                {{ $particulars['ProjectApprovalExpenses']['from_amount']['name'] }}</td>
                            <td scope="row" class="text-right">
                                {{ Helper::convertMoneyFormat($particulars['ProjectApprovalExpenses']['from_amount']['value']) }}
                            </td>
                            <td scope="row" class="text-right">
                                {{ Helper::convertMoneyFormat($particulars['ProjectApprovalExpenses']['end_amount']['value']) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-left" scope="row">{{ $particulars['OtherExpense']['from_amount']['name'] }}
                            </td>
                            <td scope="row" class="text-right">
                                {{ Helper::convertMoneyFormat($particulars['OtherExpense']['from_amount']['value']) }}
                            </td>
                            <td scope="row" class="text-right">
                                {{ Helper::convertMoneyFormat($particulars['OtherExpense']['end_amount']['value']) }}
                            </td>
                        </tr>
                        <tr>
                            <th class="text-left" scope="row">{{ $particulars['TotalCostTransferredToWorkInProcess'] }}
                            </th>
                            <th scope="row" class="text-right">
                                {{ Helper::convertMoneyFormat($particulars['ConstructionMaterialPurchases']['from_amount']['value'] + $particulars['ConstructionLabourExpense']['from_amount']['value'] + $particulars['ProjectApprovalExpenses']['from_amount']['value'] + $particulars['OtherExpense']['from_amount']['value']) }}
                            </th>
                            <th scope="row" class="text-right">
                                {{ Helper::convertMoneyFormat($particulars['ConstructionMaterialPurchases']['end_amount']['value'] + $particulars['ConstructionLabourExpense']['end_amount']['value'] + $particulars['ProjectApprovalExpenses']['end_amount']['value'] + $particulars['OtherExpense']['end_amount']['value']) }}
                            </th>
                        </tr>
                        <tr>
                            <td class="text-left" scope="row">{{ $particulars['OpeningWorkInProcess'] }}</td>
                            <td scope="row" class="text-right">0</td>
                            <td scope="row" class="text-right">0</td>
                        </tr>
                        <tr>
                            <td class="text-left" scope="row">{{ $particulars['ClosingWorkInProcess'] }}</td>
                            <td scope="row" class="text-right">0</td>
                            <td scope="row" class="text-right">0</td>
                        </tr>
                        <tr>
                            <th class="text-left" scope="row">
                                <h4>Cost of revenue </h4>
                            </th>
                            <th scope="row" class="text-right">
                                <h4>{{ Helper::convertMoneyFormat($particulars['CostOfRevenue']['from_total']) }}
                                </h4>
                            </th>
                            <th scope="row" class="text-right">
                                <h4>{{ Helper::convertMoneyFormat($particulars['CostOfRevenue']['end_total']) }}
                                </h4>
                            </th>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <br>
    </div>
@stop


