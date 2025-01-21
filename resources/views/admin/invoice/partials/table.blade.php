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
                                            <td>{{ $item->normal_weight }}</td>
                                            <td>{{ $item->electronics_weight }}</td>
                                            <td>{{ $item->msic_weight }}</td>
                                            <td>{{ $item->other_weight }}</td>
                                            <td>{{ $item->grand_total_weight }}</td>
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
                                    <div class="custom-paginate">
    {{ $items->links() }}
</div>