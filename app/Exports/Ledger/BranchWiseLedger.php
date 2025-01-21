<?php

namespace App\Exports\Ledger;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class BranchWiseLedger implements FromView
{
    protected $items;
    protected $extra;
    public function __construct($branch_wise_ledger)
    {
        $this->items = $branch_wise_ledger['items'];
        $this->extra = $branch_wise_ledger['extra'];
    }
    public function view(): View
    {
        return view('admin.accounts-report.ledger.branch-wise.exl', [
            'items' => $this->items,
            'extra' => $this->extra,
        ]);
    }
}
