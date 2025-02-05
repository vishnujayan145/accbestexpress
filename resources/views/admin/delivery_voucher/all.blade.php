@extends('layouts.app')

<?php

$moduleName = __('root.dr_voucher.dr_voucher_manage');
$createItemName = __('root.common.create') . ' ' . $moduleName;

$breadcrumbMainName = $moduleName;
$breadcrumbCurrentName = ' ' . __('root.common.all');

$breadcrumbMainIcon = 'account_balance_wallet';
$breadcrumbCurrentIcon = 'archive';

$ModelName = 'App\Transaction';
$ParentRouteName = 'delivery_voucher';

$voucher_type = 'DV';

$all = config('role_manage.CrVoucher.All');
$create = config('role_manage.CrVoucher.Create');
$delete = config('role_manage.CrVoucher.Delete');
$edit = config('role_manage.CrVoucher.Edit');
$pdf = config('role_manage.CrVoucher.Pdf');
$permanently_delete = config('role_manage.CrVoucher.PermanentlyDelete');
$restore = config('role_manage.CrVoucher.Restore');
$show = config('role_manage.CrVoucher.Show');
$trash_show = config('role_manage.CrVoucher.TrashShow');

$transaction = new \App\Transaction();
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
        <div class="block-header @if ($is_rtl) pull-right @else pull-left @endif">
            <a @if ($create==0) class="dis-none" @endif class="btn btn-sm btn-info waves-effect"
                href="{{ route('agency_voucher') }}">{{ __('root.common.create') }}</a>
        </div>
        <ol class="breadcrumb breadcrumb-col-cyan @if ($is_rtl) pull-left  @else pull-right @endif">
            <li><a href="{{ route('dashboard') }}"><i class="material-icons">home</i> {{ __('root.common.home') }}</a>
            </li>
            <li><a href="{{ route('agency_voucher.all') }}"><i
                        class="material-icons">{{ $breadcrumbMainIcon }}</i>&nbsp;{{ 'Delivery Voucher Manage' }}</a></li>
            <li class="active"><i
                    class="material-icons">{{ $breadcrumbCurrentIcon }}</i>&nbsp;{{ $breadcrumbCurrentName }}
            </li>
        </ol>
        <!-- Hover Rows -->
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="header">


                        <ul class="header-dropdown m-r--5">
                            <form class="search" action=""
                                method="get">
                                {{ csrf_field() }}
                                <input autofocus type="search" name="search" class="form-control input-sm "
                                    placeholder="{{ __('root.common.search') }}" />
                            </form>
                        </ul>
                    </div>
                    <form class="actionForm" action="" method="get">
                        <div class="pagination-and-action-area body">

                            <div>
                                <div class="custom-paginate">
                                    {{ $items->links() }}
                                </div>
                            </div>
                        </div>
                        <div class="body table-responsive">
                            {{ csrf_field() }}
                            @if ($items->count() > 0)
                            <table class="table table-hover table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <th class="checkbox_custom_style text-center">
                                            <input name="selectTop" type="checkbox" id="md_checkbox_p"
                                                class="chk-col-cyan" />
                                            <label for="md_checkbox_p"></label>
                                        </th>
                                        <th class="text-center">{{ __('root.cr_voucher.voucher_no') }}</th>

                                        <th>{{ __('root.dr_voucher.shipment_no') }}</th>
                                        <th>{{ __('root.dr_voucher.date') }}</th>
                                        <th>{{ __('root.dr_voucher.Part_name') }}</th>

                                        <th>{{'Remarks' }} </th>
                                        <th>{{ __('root.dr_voucher.options') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i = 1; ?>
                                    @foreach ($items as $item)


                                    <tr>
                                        <th class="text-center">
                                            <input name="items[id][]" value="{{ $item->voucher_no }}"
                                                type="checkbox" id="md_checkbox_{{ $i }}"
                                                class="chk-col-cyan selects " />
                                            <label for="md_checkbox_{{ $i }}"></label>
                                        </th>
                                        <td class="text-center">
                                            {{ ($item->voucher_id) }}
                                        </td>

                                        <td>{{ $item->ship_no}}</td>
                                        <td>{{ date(config('settings.date_format'), strtotime($item->date)) }}
                                        </td>
                                        <td>{{ $item->party_name ?? 'Nill' }}</td> 


                                        <td>{{ $item->remarks ?? 'Nill' }}</td>
                                        <td class="tdTrashAction">
                                            <a @if ($edit==0) class="dis-none" @endif
                                                class="m-b-3 btn btn-xs btn-info waves-effect"
                                                href="{{ route($ParentRouteName . '.edit', ['id' => $item->id]) }}"
                                                data-toggle="tooltip" data-placement="top"
                                                title="{{ __('root.common.edit') }}"><i
                                                    class="material-icons">mode_edit</i></a>

                                            <a @if ($delete==0) class="dis-none" @endif
                                                class="m-b-3 btn btn-xs btn-danger waves-effect"
                                                href="{{ route($ParentRouteName . '.delete', ['id' => $item->id]) }}"
                                                data-toggle="tooltip" data-placement="top"
                                                title="{{ __('root.common.trash') }}"> <i
                                                    class="material-icons">delete</i></a>

                                        </td>
                                    </tr>
                                    <?php $i++; ?>
                                    @endforeach
                                    <thead>
                                        <tr>
                                            <th class="checkbox_custom_style text-center">
                                                <input name="selectBottom" type="checkbox" id="md_checkbox_footer"
                                                    class="chk-col-cyan" />
                                                <label for="md_checkbox_footer"></label>
                                            </th>
                                            <th class="text-center">{{ __('root.dr_voucher.voucher_no') }}</th>

                                            <th>{{ __('root.dr_voucher.shipment_no') }}</th>
                                            <th>{{ __('root.dr_voucher.date') }}</th>
                                            <th>{{ __('root.dr_voucher.Part_name') }}</th>

                                            <th>{{'Remarks' }} </th>
                                            <th>{{ __('root.dr_voucher.options') }}</th>
                                        </tr>
                                    </thead>
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

                    </form>
                </div>
            </div>
        </div>
        <!-- #END# Hover Rows -->
    </div>
</section>
@stop

@push('include-css')
<!-- Bootstrap Select Css -->
<link href="{{ asset('asset/plugins/bootstrap-select/css/bootstrap-select.css') }}" rel="stylesheet" />
@endpush

@push('include-js')
<script>
    @if(Session::has('success'))
    toastr["success"]('{{ Session::get('
        success ') }}');
    @endif
    @if(Session::has('error'))
    toastr["error"]('{{ Session::get('
        error ') }}');
    @endif
    @if($errors -> any())
    @foreach($errors -> all() as $error)
    toastr["error"]('{{ $error }}');
    @endforeach
    @endif
</script>
{{-- All datagrid --}}
<script src="{{ asset('asset/js/dataTable.js') }}"></script>
<script>
    BaseController.init();
</script>
@endpush