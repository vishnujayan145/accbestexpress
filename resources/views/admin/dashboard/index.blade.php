@extends('layouts.app')

@section('title')
    <?php $ApplicationName = Config::get('settings.company_name'); ?>
    {{ $ApplicationName }} -> Dashboard
@stop

@section('top-bar')
    @include('includes.top-bar')
@stop

@section('left-sidebar')
    @include('includes.left-sidebar')
@stop

@push('include-css')
    <!-- Bootstrap Select Css -->
    <link href="{{ asset('asset/plugins/bootstrap-select/css/bootstrap-select.css') }}" rel="stylesheet" />
@endpush
@section('content')
    <section @if ($is_rtl) dir="rtl" @endif class="content">
        <div class="container-fluid">
            <div class="block-header">
                <h2> {{ __('root.dashboard.name') }} </h2>
            </div>
            <!-- Widgets -->
            <div class="row clearfix">
                <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
                    <div class="info-box bg-pink hover-expand-effect">
                        <div class="icon">
                            <i class="fas fa-project-diagram"></i>
                        </div>
                        <div class="content">
                            <div class="text">
                                <h4>{{ __('root.dashboard.branch') }}</h4>
                            </div>
                            <div class="number count-to" data-from="0" data-to="{{ $total_branches }}" data-speed="1000"
                                data-fresh-interval="0"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
                    <div class="info-box bg-cyan hover-expand-effect">
                        <div class="icon">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </div>
                        <div class="content">
                            <div class="text">
                                <h4>{{ __('root.dashboard.ledger_type') }}</h4>
                            </div>
                            <div class="number count-to" data-from="0" data-to="{{ $total_income_expense_types }}"
                                data-speed="1000" data-fresh-interval="20"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
                    <div class="info-box bg-purple hover-expand-effect">
                        <div class="icon">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </div>
                        <div class="content">
                            <div class="text">
                                <h4>{{ __('root.dashboard.ledger_group') }}</h4>
                            </div>
                            <div class="number count-to" data-from="0" data-to="{{ $total_income_expense_groups }}"
                                data-speed="1000" data-fresh-interval="20"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
                    <div class="info-box bg-light-green hover-expand-effect">
                        <div class="icon">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </div>
                        <div class="content">
                            <div class="text">
                                <h4>{{ __('root.dashboard.ledger') }} </h4>
                            </div>
                            <div class="number count-to" data-from="0" data-to="{{ $total_income_expense_heads }}"
                                data-speed="1000" data-fresh-interval="20"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
                    <div class="info-box bg-blue-grey hover-expand-effect">
                        <div class="icon">
                            <i class="fas fa-university"></i>
                        </div>
                        <div class="content">
                            <div class="text">
                                <h4>{{ __('root.dashboard.bank_or_cash') }}</h4>
                            </div>
                            <div class="number count-to" data-from="0" data-to="{{ $total_bank_cashes }}" data-speed="1000"
                                data-fresh-interval="20"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
                    <div class="info-box bg-orange hover-expand-effect">
                        <div class="icon">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="content">
                            <div class="text">
                                <h4>{{ __('root.dashboard.user') }}</h4>
                            </div>
                            <div class="number count-to" data-from="0" data-to="{{ $total_users }}" data-speed="1000"
                                data-fresh-interval="20"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
                    <div class="info-box bg-red hover-expand-effect">
                        <div class="icon">
                            <i class="fas fa-user-lock "></i>
                        </div>
                        <div class="content">
                            <div class="text">
                                <h4>{{ __('root.dashboard.role_manage') }}</h4>
                            </div>
                            <div class="number count-to" data-from="0" data-to="{{ $total_role_manages }}"
                                data-speed="1000" data-fresh-interval="20"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
                    <div class="info-box bg-brown hover-expand-effect">
                        <div class="icon">
                            <i class="fas fa-receipt"></i>
                        </div>
                        <div class="content">
                            <div class="text">
                                <h4>{{ __('root.dashboard.report') }}</h4>
                            </div>
                            <div class="number count-to" data-from="0" data-to="14" data-speed="1000"
                                data-fresh-interval="20"></div>
                        </div>
                    </div>
                </div>
                <!-- #END# Widgets -->
            </div>
            <!--  Quick links  -->
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="header">
                            <h2>
                                {{ __('root.dashboard.quick_access') }}
                            </h2>
                        </div>
                        <div class="body">
                            <div class="button-demo">
                                <a href="{{ route('reports.accounts.ledger') }}" type="button"
                                    class="btn bg-teal waves-effect">{{ __('root.dashboard.quick_access_ledger') }}</a>
                                <a href="{{ route('reports.accounts.trial_balance') }}" type="button"
                                    class="btn bg-green waves-effect">{{ __('root.dashboard.quick_access_trial_balance') }}</a>
                                <a href="{{ route('reports.accounts.cost_of_revenue') }}" type="button"
                                    class="btn bg-orange waves-effect">{{ __('root.dashboard.quick_access_cost_of_revenue') }}</a>
                                <a href="{{ route('reports.accounts.profit_or_loss_account') }}" type="button"
                                    class="btn bg-deep-purple waves-effect">{{ __('root.dashboard.quick_access_profit_or_loss_account') }}</a>
                                <a href="{{ route('reports.accounts.retained_earnings') }}" type="button"
                                    class="btn bg-blue waves-effect">{{ __('root.dashboard.quick_access_retained_earnings') }}</a>
                                <a href="{{ route('reports.accounts.fixed_asset_schedule') }}" type="button"
                                    class="btn bg-light-green waves-effect">{{ __('root.dashboard.quick_access_fixed_asset_schedule') }}</a>
                                <a href="{{ route('reports.accounts.balance_sheet') }}" type="button"
                                    class="btn bg-light-blue waves-effect">{{ __('root.dashboard.quick_access_balance_sheet') }}</a>
                                <a href="{{ route('reports.accounts.cash_flow') }}" type="button"
                                    class="btn bg-cyan waves-effect">{{ __('root.dashboard.quick_access_cash_flow') }}</a>
                                <a href="{{ route('reports.accounts.receive_payment') }}" type="button"
                                    class="btn bg-teal waves-effect">{{ __('root.dashboard.quick_access_receive_payment') }}</a>

                                <a href="{{ route('income_expense_type') }}" type="button"
                                    class="btn bg-light-green waves-effect">{{ __('root.dashboard.quick_access_ledger_type') }}</a>
                                <a href="{{ route('income_expense_group') }}" type="button"
                                    class="btn bg-orange waves-effect">{{ __('root.dashboard.quick_access_ledger_group') }}</a>

                                <a href="{{ route('dr_voucher') }}" type="button"
                                    class="btn bg-lime waves-effect">{{ __('root.dashboard.quick_access_debit_voucher') }}</a>
                                <a href="{{ route('cr_voucher') }}" type="button"
                                    class="btn bg-brown waves-effect">{{ __('root.dashboard.quick_access_credit_voucher') }}</a>
                                <a href="{{ route('jnl_voucher') }}" type="button"
                                    class="btn bg-deep-orange waves-effect">{{ __('root.dashboard.quick_access_journal_voucher') }}</a>
                                <a href="{{ route('contra_voucher') }}" type="button"
                                    class="btn bg-orange waves-effect">{{ __('root.dashboard.quick_access_contra_voucher') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Chart -->
            <div class="row clearfix">
                <!-- Line Chart -->
                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="header">
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                    <h2>{{ __('root.dashboard.graph_profit_or_loss') }}</h2>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 float-r">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <select data-live-search="true" class="form-control  show-tick"
                                                name="year" id="profit_or_loss_year">
                                                <option value="0">Select year</option>
                                                @foreach ($years as $year)
                                                    <option @if ($year == date('Y')) selected @endif
                                                        value="{{ $year }}">{{ $year }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="body">
                            <canvas id="profit_or_loss_graph" height="150"></canvas>
                        </div>
                    </div>
                </div>
                <!-- #END# Line Chart -->
                <!-- Bar Chart -->
                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="header">
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                    <h2>{{ __('root.dashboard.graph_total_voucher') }}</h2>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 float-r">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <select data-live-search="true" class="form-control  show-tick"
                                                name="year" id="voucher_year">
                                                <option value="0">Select year</option>
                                                @foreach ($years as $year)
                                                    <option @if ($year == date('Y')) selected @endif
                                                        value="{{ $year }}">{{ $year }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="body">
                            <canvas id="bar_chart" height="150"></canvas>
                        </div>
                    </div>
                </div>
                <!-- #END# Bar Chart -->
            </div>
        </div>
    </section>
@stop
@push('include-js')
    <!-- Jquery CountTo Plugin Js -->
    <script src="{{ asset('asset/plugins/jquery-countto/jquery.countTo.js') }}"></script>
    <!-- ChartJs -->
    <script src="{{ asset('asset/plugins/chartjs/chart.min.js') }}"></script>
    <script>
        @if (Session::has('success'))
            toastr["success"]('{{ Session::get('success') }}');
        @endif
        @if (Session::has('error'))
            toastr["error"]('{{ Session::get('error') }}');
        @endif
        @if ($errors->any())
            @foreach ($errors->all() as $error)
                toastr["error"]('{{ $error }}');
            @endforeach
        @endif
        $(function() {
            //Widgets count
            $('.count-to').countTo();
            // voucher total graph by ajax
            function getTotalVoucher(year) {
                return new Promise((resolve, reject) => {
                    let datasets;
                    Helper.ajaxRequest('GET', `total-voucher/${year}`).then((res) => {
                        let data = res.data;
                        datasets = [{
                                label: "{{ __('root.dashboard.graph_total_dr_voucher') }}",
                                data: data.total_dr_voucher,
                                borderColor: 'rgba(255, 71, 87, .75)',
                                backgroundColor: 'rgba(255, 71, 87,.8)',
                                pointBorderColor: 'rgba(255, 71, 87,.0)',
                                pointBackgroundColor: 'rgba(255, 71, 87,.90)',
                                pointBorderWidth: 1
                            },
                            {
                                label: "{{ __('root.dashboard.graph_total_cr_voucher') }}",
                                data: data.total_cr_voucher,
                                borderColor: 'rgba(46, 213, 115, .75)',
                                backgroundColor: 'rgba(46, 213, 115,.8)',
                                pointBorderColor: 'rgba(46, 213, 115, .0)',
                                pointBackgroundColor: 'rgba(46, 213, 115,.90)',
                                pointBorderWidth: 1
                            },
                            {
                                label: "{{ __('root.dashboard.graph_total_cnt_voucher') }}",
                                data: data.total_cnt_voucher,
                                borderColor: 'rgba(255, 165, 2, .75)',
                                backgroundColor: 'rgba(255, 165, 2, .8)',
                                pointBorderColor: 'rgba(255, 165, 2, 0)',
                                pointBackgroundColor: 'rgba(255, 165, 2,.9)',
                                pointBorderWidth: 1
                            },
                            {
                                label: "{{ __('root.dashboard.graph_total_jnl_voucher') }}",
                                data: data.total_jnl_voucher,
                                borderColor: 'rgba(83, 82, 237,.75)',
                                backgroundColor: 'rgba(83, 82, 237,.8)',
                                pointBorderColor: 'rgba(83, 82, 237,0)',
                                pointBackgroundColor: 'rgba(83, 82, 237,.9)',
                                pointBorderWidth: 1
                            }
                        ];
                        resolve(datasets);
                    }).catch((error) => {
                        reject(0);
                    });
                });
            }

            function getProfitOrLoss(year) {
                return new Promise((resolve, reject) => {
                    let datasets;
                    Helper.ajaxRequest('GET', `profit-loss/${year}`).then((res) => {
                        let res_data = res.data;
                        datasets = [{
                                label: "{{ __('root.dashboard.graph_revenue') }}",
                                data: res_data.revenue,
                                borderColor: 'rgba(58, 227, 116,1.0)',
                                fill: false,
                            },
                            {
                                label: "{{ __('root.dashboard.graph_cost_of_revenue') }}",
                                data: res_data.CostOfRevenue,
                                borderColor: 'rgba(255, 242, 0,1.0)',
                                fill: false,
                            },
                            {
                                label: "{{ __('root.dashboard.graph_gross_profit') }}",
                                data: res_data.GrossProfit,
                                borderColor: 'rgba(183, 21, 64,1.0)',
                                fill: false,
                            },
                            {
                                label: "{{ __('root.dashboard.graph_indirect_income') }}",
                                data: res_data.IndirectIncome,
                                borderColor: 'rgba(23, 192, 235,1.0)',
                                fill: false,
                            },
                            {
                                label: "{{ __('root.dashboard.graph_income_from_operation') }}",
                                data: res_data.IncomeFromOperation,
                                borderColor: 'rgba(247, 183, 49,1.0)',
                                fill: false,
                            },
                            {
                                label: "{{ __('root.dashboard.graph_administrative_expense') }}",
                                data: res_data.AdministrationExpenses,
                                borderColor: 'rgba(113, 88, 226,1.0)',
                                fill: false,
                            },
                            {
                                label: "{{ __('root.dashboard.graph_income_before_tax_and_interest') }}",
                                data: res_data.IncomeBeforeTaxAndInterest,
                                borderColor: 'rgba(44, 62, 80,1.0)',
                                fill: false,
                            },
                            {
                                label: "{{ __('root.dashboard.graph_financial_expense') }}",
                                data: res_data.FinancialExpense,
                                borderColor: 'rgba(197, 108, 240,1.0)',
                                fill: false,
                            },
                            {
                                label: "{{ __('root.dashboard.graph_net_profit_loss') }}",
                                data: res_data.NetProfitOrLoss,
                                borderColor: 'rgba(255, 56, 56,1.0)',
                                fill: false,
                            }
                        ];
                        resolve(datasets);
                    }).catch((error) => {
                        reject(0);
                    });
                });
            }

            // load current year
            const labels = [
                "Jan",
                "Feb",
                "Mar",
                "Apr",
                "May",
                "June",
                "July",
                "Aug",
                "Sept",
                "Oct",
                "Nov",
                "Dec"
            ];
            // total voucher graph
            const bar_chart = document.getElementById("bar_chart");
            let barChart;
            getTotalVoucher(new Date().getFullYear()).then((datasets) => {
                barChart = new Chart(bar_chart, Helper.configChart('bar', labels, datasets));
            }).catch((error) => {
                return 0;
            });
            document.getElementById("voucher_year").addEventListener('change', (e) => {
                getTotalVoucher(e.target.value).then((datasets) => {
                    barChart.destroy();
                    barChart = new Chart(bar_chart, Helper.configChart('bar', labels, datasets));
                }).catch((error) => {
                    console.log(error);
                });
            });
            // Profit or loss graph
            const profit_or_loss_graph = document.getElementById("profit_or_loss_graph");
            let profitOrLossGraph;
            getProfitOrLoss(new Date().getFullYear()).then((datasets) => {
                profitOrLossGraph = new Chart(profit_or_loss_graph, Helper.configChart('line', labels,
                    datasets));
            }).catch((error) => {
                return 0;
            });
            document.getElementById("profit_or_loss_year").addEventListener('change', (e) => {
                getProfitOrLoss(e.target.value).then((datasets) => {
                    profitOrLossGraph.destroy();
                    profitOrLossGraph = new Chart(profit_or_loss_graph, Helper.configChart('line',
                        labels, datasets));
                }).catch((error) => {
                    console.log(error);
                });
            });

        });
    </script>
    {{-- All datagrid --}}
    <script src="{{ Helper::assetV('asset/js/dataTable.js') }}"></script>
    <script>
        BaseController.init();
    </script>
@endpush
