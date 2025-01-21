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
