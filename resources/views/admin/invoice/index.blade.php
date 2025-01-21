@extends('layouts.app')

<?php

$moduleName = 'Invoice';
$createItemName = __('root.common.create') . ' ' . $moduleName;

$breadcrumbMainName = $moduleName;
$breadcrumbCurrentName = ' ' . __('root.common.all');

$breadcrumbMainIcon = 'local_shipping';
$breadcrumbCurrentIcon = 'archive';

$ModelName = 'App\Shipment';
$ParentRouteName = 'invoice';

$voucher_type = 'DV';

?>

@section('title')
    {{ $moduleName }} -> {{ $breadcrumbCurrentName }}
@stop
@section('top-bar')
    @include('includes.top-bar')
@stop
@section('left-sidebar')
    @include('includes.left-sidebar')
@stop
@section('content')
    <section @if ($is_rtl) dir="rtl" @endif class="content">
        <div class="container-fluid">
            
            <ol class="breadcrumb breadcrumb-col-cyan @if ($is_rtl) pull-left  @else pull-right @endif">
                <li><a href="{{ route('dashboard') }}"><i class="material-icons">home</i> {{ __('root.common.home') }}</a>
                </li>
                <li><a href="{{ route($ParentRouteName) }}"><i
                            class="material-icons">{{ $breadcrumbMainIcon }}</i>&nbsp;{{ $breadcrumbMainName }}</a></li>
                <li class="active"><i
                        class="material-icons">{{ $breadcrumbCurrentIcon }}</i>&nbsp;{{ $breadcrumbCurrentName }}
                </li>
            </ol>
            
            <!-- Hover Rows -->
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="header">
                            <a class="btn btn-xs btn-info waves-effect"
                                href="{{ route($ParentRouteName) }}">{{ __('root.common.all') }}({{ $items->count() }})</a>
                           
                            <ul class="header-dropdown m-r--5">
                                <form class="search" action="{{ route('invoice.active.search') }}"
                                    method="get">
                                    {{ csrf_field() }}
                                    <div style="display: flex; align-items: center;">
                                    <input type="text" id="search" name="search" class="form-control input-sm" 
                                        placeholder="{{ __('root.common.search') }}" autocomplete="off" />
                                    <div class="" style="margin-left: 10px;">
                                        <input class="btn btn-sm btn-info waves-effect" type="submit" value="Submit" name="submit">
                                    </div>
                                </div>

                                </form>
                            </ul>
                        </div>
                        <form class="actionForm" action="{{ route('invoice.active.action') }}" method="get">
                        <div class="pagination-and-action-area body">
        <div>
            <div class="select-and-apply-area">
                <!-- Date Filter Inputs -->
                <div class="form-group">
                    <div class="form-line">
                        <input type="date" name="start_date" class="form-control" placeholder="Start Date" value="{{ request('start_date') }}" />
                    </div>
                </div>
                <div class="form-group">
                    <div class="form-line">
                        <input type="date" name="end_date" class="form-control" placeholder="End Date" value="{{ request('end_date') }}" />
                    </div>
                </div>
                <div class="form-group">
                    <input class="btn btn-sm btn-info waves-effect" type="submit" value="{{ __('root.common.filter') }}" name="Filter">
                </div>
            </div>
        </div>
       
    </div>
                            <div class="body table-responsive">
                                {{ csrf_field() }}
                                @if ($items && $items->count() > 0)
                                    <table class="table table-hover table-bordered table-sm">
                                    <thead>
                                    <tr>
                                   
                                        <th class="text-center">{{ __('Booking Number') }}</th>
                                        <th>{{ __('Branch') }}</th>
                                        <th>{{ __('Created Date') }}</th>
                                        <th>{{ __('Courier Company') }}</th>
                                        <th>{{ __('Normal Weight') }}</th>
                                        <th>{{ __('Electronics Weight') }}</th>
                                        <th>{{ __('Misc Weight') }}</th>
                                        <th>{{ __('Other Weight') }}</th>
                                        <th>{{ __('Total Weight') }}</th>
                                        <th>{{ __('Number of Pieces') }}</th>
                                        <th>{{ __('Total Amount') }}</th>                                        
                                        <th>{{ __('Box Packing Charge') }}</th>
                                        <th>{{ __('Other Packing Charge') }}</th>
                                        <th>{{ __('Document Charge') }}</th>
                                        <th>{{ __('Total Freight') }}</th>
                                        <th>{{ __('Payment Method') }}</th>
                                        <th>{{ __('Other Charges') }}</th>
                                        <th>{{ __('Shipping Method') }}</th>
                                        <th>{{ __('Grand Total') }}</th>
                                        <th>{{ __('Discount') }}</th>
                                        <!--<th>{{ __('Options') }}</th>-->
                                    </tr>
                                </thead>

                                        <tbody>
                                            <?php $i = 1; ?>
                                            @foreach ($items as $item)
                                            <tr>
                                           
                                            <td class="text-center">{{ $item->booking_number }}</td>
                                            <td>{{ $item->name }}</td>
                                            <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d-m-Y') }}</td>
                                            <td>{{ $item->courier_company }}</td>
                                            <td>{{ number_format($item->normal_weight, 3) }}</td>
                                            <td>{{ $item->electronics_weight }}</td>
                                            <td>{{ $item->msic_weight }}</td>
                                            <td>{{ $item->other_weight }}</td>
                                            <td>{{ number_format($item->grand_total_weight,3)}}</td>
                                            <td>{{ $item->number_of_pcs }}</td>
                                            <td>{{ $item->amount_grand_total}}</td>
                                            <td>{{ $item->packing_charge }}</td>                                            
                                            <td>{{ $item->other_charges }}</td>
                                            <td>{{ $item->document_charge }}</td>
                                            <td>{{ $item->total_freight }}</td>
                                            <td>{{ $item->payment_method }}</td>
                                            <td>{{ $item->other_charges }}</td>
                                            <td>{{ $item->shiping_method }}</td>
                                            <td>{{ $item->grand_total }}</td>
                                            <td>{{ $item->discount }}</td>
                                           
                                        </tr>
                                                <?php $i++; ?>
                                            @endforeach
                                          

                                        </tbody>
                                    </table>
                                  
                                @else
                                    <div class="body table-responsive">
                                        <table class="table table-hover table-bordered">
                                            <thead>
                                                <tr>
                                                    <th colspan="8" class="text-danger text-center">
                                                        {{ __('root.common.no_data_found') }}
                                                    </th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                @endif
                            </div>
                            <div class="pagination-and-action-area body">
                                <div>
                                   
                                </div>
                                <div>
                                    <div class="custom-paginate">
                                        {{ $items->links() }}
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- #END# Hover Rows -->
        </div>
    </section>
@stop
@section('custom-page-script')
    <script>
        $(document).on("click", ".selects", function() {
            var total = $(".selects").length;
            var checked = $(".selects:checked").length;
            if (total == checked) {
                $("input[name=selectTop]").prop("checked", true);
                $("input[name=selectBottom]").prop("checked", true);
            } else {
                $("input[name=selectTop]").prop("checked", false);
                $("input[name=selectBottom]").prop("checked", false);
            }
        });
        $(document).on("click", "input[name=selectTop]", function() {
            if ($(this).prop("checked")) {
                $(".selects").prop("checked", true);
                $("input[name=selectBottom]").prop("checked", true);
            } else {
                $(".selects").prop("checked", false);
                $("input[name=selectBottom]").prop("checked", false);
            }
        });
        $(document).on("click", "input[name=selectBottom]", function() {
            if ($(this).prop("checked")) {
                $(".selects").prop("checked", true);
                $("input[name=selectTop]").prop("checked", true);
            } else {
                $(".selects").prop("checked", false);
                $("input[name=selectTop]").prop("checked", false);
            }
        });
    </script>
    <script>
    $(document).ready(function () {
        $('#search').on('keyup', function () {
            let query = $(this).val();

            if (query.length > 0) {
                $.ajax({
                    url: "{{ route('invoice.active.search') }}",
                    type: "GET",
                    data: {
                        search: query,
                    },
                    success: function (data) {
                        $('.body.table-responsive').html(data);
                    }
                });
            } else {
                // Optionally handle the case where the input is cleared, e.g., reload the original data
                $.ajax({
                    url: "{{ route('invoice.active.search') }}",
                    type: "GET",
                    data: {
                        search: "",
                    },
                    success: function (data) {
                        $('.body.table-responsive').html(data);
                    }
                });
            }
        });
    });
</script>

@stop
