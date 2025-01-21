<?php

namespace App\Http\Controllers;

use App\Transaction;
use Illuminate\Http\Request;
use App\BankCash;
use Barryvdh\DomPDF\Facade as PDF;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

class DrVoucherController extends Controller
{


    //    Important properties
    public $parentModel = Transaction::class;
    public $parentRoute = 'dr_voucher';
    public $parentView = "admin.dr-voucher";

    public $voucher_type = "DV";

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [];
        if (Cache::get('count_trashed_dv') && Cache::get('count_trashed_dv') != null) {
            $data['count_trashed_dv'] = Cache::get('count_trashed_dv');
        } else {
            $data['count_trashed_dv'] = $this->parentModel::onlyTrashed()->where('voucher_type', $this->voucher_type)->count();
            Cache::put('count_trashed_dv', $data['count_trashed_dv']);
        }
        $items = $this->parentModel::where('voucher_type', $this->voucher_type)
            ->with('Branch', 'IncomeExpenseHead', 'BankCash')
            ->orderBy('voucher_no', 'desc')
            ->paginate(60);
        return view($this->parentView . '.index', $data)->with('items', $items);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['bank_cashes'] = BankCash::all();
        return view($this->parentView . '.create',$data, $this->__getBranchBankCashIncomeExpenseHead());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'branch_id' => 'required',
            'bank_cash_id' => 'required',
            'voucher_date' => 'required',
            'reference_no' => 'required',
        ]);
        $date = new \DateTime($request->voucher_date);
        $voucher_date = $date->format('Y-m-d'); // 31-07-2012 '2008-11-11'
        $voucher_info = $this->parentModel::where('voucher_type', $this->voucher_type)
            ->withTrashed()
            ->orderBy('voucher_no', 'DESC')
            ->first();
        if (!empty($voucher_info)) {
            $voucher_no = $voucher_info->voucher_no + 1;
        } else {
            $voucher_no = 1;
        }
        $reference_no = $request->reference_no;
        foreach ($request->income_expense_head_id as $key => $id) {
            if ($id == 0) {
                continue;
            }
            $chq_no = null;
            if ($key == 0) {
                $chq_no = $request->cheque_number;
            }
            $this->parentModel::create([
                'voucher_no' => $voucher_no,
                'branch_id' => $request->branch_id,
                'bank_cash_id' => $request->bank_cash_id,
                'cheque_number' => $chq_no,
                'voucher_type' => $this->voucher_type,
                'voucher_date' => $voucher_date,
                'reference_no' =>$reference_no,
                'particulars' => $request->particulars,
                'income_expense_head_id' => $id,
                'dr' => $request->amount[$key],
                'created_by' => auth()->user()->email,
            ]);
        }
        Cache::forget('count_dv');
        Session::flash('success', "Successfully  Create");
        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        //$item = $this->parentModel::find($request->id);
        $id = $request->id;
        $item = $this->parentModel::where('voucher_type', '=', $this->voucher_type)
            ->where(function ($q) use ($id) {
                $q->where('voucher_no', '=', $id);
            })
            ->get();
        if (count($item) == 0) {
            Session::flash('error', "Item not found");
            return redirect()->back();
        }
        return view($this->parentView . '.show')->with('items', $item);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $items = $this->parentModel::where('voucher_type', '=', $this->voucher_type)
            ->where(function ($q) use ($id) {
                $q->where('voucher_no', '=', $id);
            })
            ->get();
        if (count($items) <= 0) {
            Session::flash('error', "Item not found");
            return redirect()->back();
        }
        foreach ($items as $item) {
            if (($item->dr) > 0) {
                $item['amount'] = $item->dr;
            } else {
                $item['amount'] = $item->cr;
            }
        }
        $data['bank_cashes'] = BankCash::all();
        return view($this->parentView . '.edit',$data, $this->__getBranchBankCashIncomeExpenseHead())->with('item', $items);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'branch_id' => 'required',
            'bank_cash_id' => 'required',
            'voucher_date' => 'required',
        ]);
        $date = new \DateTime($request->voucher_date);
        $voucher_date = $date->format('Y-m-d'); // 31-07-2012 '2008-11-11'
        $items = $this->parentModel::where('voucher_type', '=', $this->voucher_type)
            ->where(function ($q) use ($id) {
                $q->where('voucher_no', '=', $id);
            })
            ->get();
        $created_by = $items[0]->created_by;
        $this->kill($id); /// Old Item kill then new items created
        foreach ($request->income_expense_head_id as $key => $income_expense_head_id) {
            if ($income_expense_head_id == 0) {
                continue;
            }
            $chq_no = null;
            if ($key == 0) {
                $chq_no = $request->cheque_number;
            }
            $this->parentModel::create([
                'voucher_no' => $id,
                'branch_id' => $request->branch_id,
                'bank_cash_id' => $request->bank_cash_id,
                'cheque_number' => $chq_no,
                'voucher_type' => $this->voucher_type,
                'voucher_date' => $voucher_date,
                'particulars' => $request->particulars,
                'income_expense_head_id' => $income_expense_head_id,
                'dr' => $request->amount[$key],
                'created_by' => $created_by,
                'updated_by' => auth()->user()->email,
            ]);
        }
        Session::flash('success', "Update Successfully");
        return redirect()->route($this->parentRoute);
    }

    public function pdf(Request $request)
    {
        $id = $request->id;
        $item = $this->parentModel::where('voucher_type', '=', $this->voucher_type)
            ->where(function ($q) use ($id) {
                $q->where('voucher_no', '=', $id);
            })
            ->get();
        if (count($item) == 0) {
            Session::flash('error', "Item not found");
            return redirect()->route($this->parentRoute);
        }
        $now = new \DateTime();
        $date = $now->format(Config('settings.date_format') . ' h:i:s');
        $extra = array(
            'current_date_time' => $date,
            'module_name' => 'Debit Voucher Report',
            'voucher_type' => 'DEBIT VOUCHER'
        );
        // return view('admin.dr-voucher.pdf');
        $pdf = PDF::loadView($this->parentView . '.pdf', ['items' => $item, 'extra' => $extra])->setPaper('a4', 'landscape');

        // return $pdf->stream($extra['current_date_time'] . '_' . $extra['module_name'] . '.pdf');
        return $pdf->download($extra['current_date_time'] . '_' . $extra['module_name'] . '.pdf');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $items = $this->parentModel::where('voucher_type', '=', $this->voucher_type)
            ->where(function ($q) use ($id) {
                $q->where('voucher_no', '=', $id);
            })
            ->get();
        if (count($items) < 1) {
            Session::flash('error', "Item not found");
            return redirect()->back();
        }
        foreach ($items as $item) {
            $item->deleted_by = auth()->user()->email;
            $item->save();
        }
        foreach ($items as $item) {
            $item->delete();
        }
        Cache::forget('count_trashed_dv');
        Cache::forget('count_dv');
        Session::flash('success', "Successfully Trashed");
        return redirect()->back();
    }
    public function trashed()
    {
        $data = [];
        if (Cache::get('count_cv') && Cache::get('count_cv') != null) {
            $data['count_cv'] = Cache::get('count_cv');
        } else {
            $data['count_cv'] = $this->parentModel::where('voucher_type', $this->voucher_type)->count();
            Cache::put('count_cv', $data['count_cv']);
        }
        $items = $this->parentModel::onlyTrashed()
            ->where('voucher_type', $this->voucher_type)
            ->with('Branch', 'IncomeExpenseHead', 'BankCash')
            ->orderBy('deleted_at', 'desc')
            ->paginate(60);
        return view($this->parentView . '.trashed', $data)
            ->with("items", $items);
    }
    public function restore($id)
    {
        $items = $this->parentModel::where('voucher_type', '=', $this->voucher_type)
            ->onlyTrashed()
            ->where(function ($q) use ($id) {
                $q->where('voucher_no', '=', $id);
            })
            ->onlyTrashed()
            ->get();
        foreach ($items as $item) {
            $item->restore();
            $item->updated_by = auth()->user()->email;
            $item->save();
        }
        Cache::forget('count_trashed_dv');
        Cache::forget('count_dv');
        Session::flash('success', 'Successfully Restore');
        return redirect()->back();
    }

    public function kill($id)
    {
        $items = $this->parentModel::where('voucher_type', '=', $this->voucher_type)
            ->withTrashed()
            ->where(function ($q) use ($id) {
                $q->where('voucher_no', '=', $id);
            })
            ->withTrashed()
            ->get();
        foreach ($items as $item) {
            $item->forceDelete();
        }
        Cache::forget('count_trashed_dv');
        Cache::forget('count_dv');
        Session::flash('success', 'Permanently Delete');
        return redirect()->back();
    }

    public function activeSearch(Request $request)
    {
        $data = [];
        if (Cache::get('count_trashed_dv') && Cache::get('count_trashed_dv') != null) {
            $data['count_trashed_dv'] = Cache::get('count_trashed_dv');
        } else {
            $data['count_trashed_dv'] = $this->parentModel::onlyTrashed()->where('voucher_type', $this->voucher_type)->count();
            Cache::put('count_trashed_dv', $data['count_trashed_dv']);
        }
        $search = $request["search"];
        $items = $this->parentModel::where('voucher_type', '=', $this->voucher_type)
            ->where(function ($q) use ($search) {
                $q->where('voucher_no', '=', $search)
                    ->orWhere('voucher_date', 'like', date("Y-m-d", strtotime($search)))
                    ->orWhere('dr', '=', $search)
                    ->orWhere('cheque_number', '=', $search)
                    ->orWhere('cr', '=', $search)
                    ->orWhere('reference_no', '=', $search)
                    ->orWhere('particulars', 'like', '%' . $search . '%')
                    ->orWhereHas('BankCash', function ($query) use ($search) {
                        $query->where('name', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('IncomeExpenseHead', function ($query) use ($search) {
                        $query->where('name', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('Branch', function ($query) use ($search) {
                        $query->where('name', 'like', '%' . $search . '%');
                    });
            })
            ->with('Branch', 'IncomeExpenseHead', 'BankCash')
            ->paginate(60);
        return view($this->parentView . '.index', $data)
            ->with('items', $items);
    }

    public function trashedSearch(Request $request)
    {
        $data = [];
        if (Cache::get('count_dv') && Cache::get('count_dv') != null) {
            $data['count_dv'] = Cache::get('count_dv');
        } else {
            $data['count_dv'] = $this->parentModel::where('voucher_type', $this->voucher_type)->count();
            Cache::put('count_dv', $data['count_dv']);
        }
        $search = $request["search"];
        $items = $this->parentModel::where('voucher_type', '=', $this->voucher_type)
            ->onlyTrashed()
            ->where(function ($q) use ($search) {
                $q->where('voucher_no', '=', $search)
                    ->orWhere('voucher_date', 'like', date("Y-m-d", strtotime($search)))
                    ->orWhere('cheque_number', '=', $search)
                    ->orWhere('dr', '=', $search)
                    ->orWhere('cr', '=', $search)
                    ->orWhere('particulars', 'like', '%' . $search . '%')
                    ->orWhereHas('BankCash', function ($query) use ($search) {
                        $query->where('name', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('IncomeExpenseHead', function ($query) use ($search) {
                        $query->where('name', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('Branch', function ($query) use ($search) {
                        $query->where('name', 'like', '%' . $search . '%');
                    });
            })
            ->onlyTrashed()
            ->with('Branch', 'IncomeExpenseHead', 'BankCash')
            ->orderBy('created_at', 'desc')
            ->paginate(60);

        return view($this->parentView . '.trashed', $data)
            ->with('items', $items);
    }

    //    Fixed Method for all
    public function activeAction(Request $request)
    {
        $request->validate([
            'items' => 'required'
        ]);
        if ($request->apply_comand_top == 3 || $request->apply_comand_bottom == 3) {
            foreach ($request->items["id"] as $id) {
                $items = $this->parentModel::where('voucher_type', '=', $this->voucher_type)
                    ->where(function ($q) use ($id) {
                        $q->where('voucher_no', '=', $id);
                    })
                    ->get();
                if (count($items) < 1) {
                    continue;
                }
                $this->destroy($id);
            }
            return redirect()->back();
        } elseif ($request->apply_comand_top == 2 || $request->apply_comand_bottom == 2) {
            foreach ($request->items["id"] as $id) {
                $items = $this->parentModel::where('voucher_type', '=', $this->voucher_type)
                    ->withTrashed()
                    ->where(function ($q) use ($id) {
                        $q->where('voucher_no', '=', $id);
                    })
                    ->withTrashed()
                    ->get();
                if (count($items) < 1) {
                    continue;
                }
                $this->kill($id);
            }
            return redirect()->back();
        } else {
            Session::flash('error', "Something is wrong.Try again");
            return redirect()->back();
        }
    }

    public function trashedAction(Request $request)
    {
        $request->validate([
            'items' => 'required'
        ]);
        if ($request->apply_comand_top == 1 || $request->apply_comand_bottom == 1) {
            foreach ($request->items["id"] as $id) {
                $items = $this->parentModel::where('voucher_type', '=', $this->voucher_type)
                    ->onlyTrashed()
                    ->where(function ($q) use ($id) {
                        $q->where('voucher_no', '=', $id);
                    })
                    ->onlyTrashed()
                    ->get();
                if (count($items) < 1) {
                    continue;
                }
                $this->restore($id);
            }
        } elseif ($request->apply_comand_top == 2 || $request->apply_comand_bottom == 2) {
            foreach ($request->items["id"] as $id) {
                $items = $this->parentModel::where('voucher_type', '=', $this->voucher_type)
                    ->onlyTrashed()
                    ->where(function ($q) use ($id) {
                        $q->where('voucher_no', '=', $id);
                    })
                    ->onlyTrashed()
                    ->get();
                if (count($items) < 1) {
                    continue;
                }
                $this->kill($id);
            }
            return redirect()->back();
        } else {
            Session::flash('error', "Something is wrong.Try again");
            return redirect()->back();
        }
        return redirect()->back();
    }
    public function __getBranchBankCashIncomeExpenseHead()
    {
        # code... 
        $data = [];
        if (Cache::get('branches') && Cache::get('branches') != null) {
            $data['branches'] = Cache::get('branches');
        } else {
            $data['branches'] = Branch::all();
            Cache::put('branches', $data['branches']);
        }
        if (!Cache::has('bank_cashes')) {
            $data['bank_cashes'] = BankCash::whereNull('deleted_by')->get();
            Cache::put('bank_cashes', $data['bank_cashes']);
        } else {
            $data['bank_cashes'] = Cache::get('bank_cashes');
        }
        if (Cache::get('income_expense_heads') && Cache::get('income_expense_heads') != null) {
            $data['income_expense_heads'] = Cache::get('income_expense_heads');
        } else {
            $data['income_expense_heads'] = IncomeExpenseHead::all();
            Cache::put('income_expense_heads', $data['income_expense_heads']);
        }
        return $data;
    }
}
