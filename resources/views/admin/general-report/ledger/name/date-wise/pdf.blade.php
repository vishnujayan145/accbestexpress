@extends('layouts.pdf')
@push('include-css')
    <link rel="stylesheet" href="{{ asset('asset/css/main-report.css') }}">
@endpush

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
                            <td class="text-right">From Date:</td>
                            <th>{{ $search_by['from'] }}</th>
                            <td class="text-right">To Date:</td>
                            <th>{{ $search_by['to'] }}</th>
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
                                Sl.No
                            </th>
                            <th>Name</th>
                            <th>Ledger Type Name</th>
                            <th>Ledger Group Name</th>
                            <th>Unit</th>
                            <th>Dr?</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $sl = 1;
                        @endphp
                        @foreach ($items as $item)
                            <tr>
                                <td class="text-center">{{ $sl }}</td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->IncomeExpenseType ? $item->IncomeExpenseType->name : '' }}
                                </td>
                                <td>{{ $item->IncomeExpenseGroup ? $item->IncomeExpenseGroup->name : '' }}
                                </td>
                                <td>{{ $item->unit }}</td>
                                @if ($item->type == '1')
                                    <td>
                                        Yes
                                    </td>
                                @else
                                    <td>
                                        No
                                    </td>
                                @endif
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
