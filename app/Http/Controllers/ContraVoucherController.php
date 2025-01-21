<?php

namespace App\Http\Controllers;

use App\Transaction;
use Illuminate\Http\Request;
use App\BankCash;
use App\Branch;

use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

class ContraVoucherController extends Controller
{


    //    Important properties
    public $parentModel = Transaction::class;
    public $parentRoute = 'contra_voucher';
    public $parentView = "admin.cnt-voucher";

    public $voucher_type = "Contra";

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [];
        if (Cache::get('count_trashed_cnt') && Cache::get('count_trashed_cnt') != null) {
            $data['count_trashed_cnt'] = Cache::get('count_trashed_cnt');
        } else {
            $data['count_trashed_cnt'] = $this->parentModel::onlyTrashed()->where('voucher_type', $this->voucher_type)->count();
            Cache::put('count_trashed_cnt', $data['count_trashed_cnt']);
        }
        $items = $this->parentModel::where('voucher_type', $this->voucher_type)
            ->orderBy('voucher_no', 'DESC')
            ->with('Branch', 'BankCash')
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
       // $cv = new CrVoucherController();
        $data['bank_cashes'] = BankCash::all();
        return view($this->parentView . '.create',$data, $this->__getBranchBankCashIncomeExpenseHead());
       // return view($this->parentView . '.create', $cv->__getBranchBankCashIncomeExpenseHead());
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
            'bank_cash_id_cr' => 'required',
            'amount' => 'required',
            'voucher_date' => 'required',
        ]);
        $date = new \DateTime($request->voucher_date);
        $reference_no = $request->reference_no;

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
        $check_number_dr = null;
        $check_number_cr = null;
        if ($request->bank_cash_id > 1) {
            $check_number_dr = $request->cheque_number;
        } else {
            $check_number_cr = $request->cheque_number;
        }
        DB::beginTransaction();
        try {
            $this->parentModel::create([
                'voucher_no' => $voucher_no,
                'branch_id' => $request->branch_id,
                'bank_cash_id' => $request->bank_cash_id,
                'cheque_number' => $check_number_dr,
                'voucher_type' => $this->voucher_type,
                'voucher_date' => $voucher_date,
                'particulars' => $request->particulars,
                'reference_no' =>$reference_no,

                'dr' => $request->amount,
                'created_by' => auth()->user()->email,
            ]);
            $this->parentModel::create([
                'voucher_no' => $voucher_no,
                'branch_id' => $request->branch_id,
                'bank_cash_id' => $request->bank_cash_id_cr,
                'cheque_number' => $check_number_cr,
                'voucher_type' => $this->voucher_type,
                'voucher_date' => $voucher_date,
                'particulars' => $request->particulars,
                'reference_no' =>$reference_no,

                'cr' => $request->amount,
                'created_by' => auth()->user()->email,
            ]);
            DB::commit();
            Session::flash('success', "Successfully  Create");
            Cache::forget('count_trashed_cnt');
            Cache::forget('count_cnt');
        } catch (\Exception $e) {
            DB::rollBack();
            Session::flash('error', $e->getMessage());
        }
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
        $id = $request->id;
        $item = $this->parentModel::where('voucher_type', '=', $this->voucher_type)
            ->where(function ($q) use ($id) {
                $q->where('voucher_no', '=', $id);
            })
            ->get();
        if (count($item) == 'on') {
            Session::flash('error', "Item not found");
            return redirect()->route($this->parentRoute);
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
       // $cv_controller = new CrVoucherController();
       $data['bank_cashes'] = BankCash::all();
       return view($this->parentView . '.edit',$data, $this->__getBranchBankCashIncomeExpenseHead())->with('items', $items);
       // return view($this->parentView . '.edit', $cv_controller->__getBranchBankCashIncomeExpenseHead())->with('items', $items);
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
            'bank_cash_id_cr' => 'required',
            'amount' => 'required',
            'voucher_date' => 'required',
        ]);
        if ($request->bank_cash_id == $request->bank_cash_id_cr and $request->bank_cash_id != 0 and $request->bank_cash_id_cr != 0) {
            Session::flash('error', "Bank Cash ( Dr ) and Bank Cash ( Cr ) should not same");
            return redirect()->back();
        }
        $date = new \DateTime($request->voucher_date);
        $voucher_date = $date->format('Y-m-d'); // 31-07-2012 '2008-11-11'
        $items = $this->parentModel::where('voucher_type', '=', $this->voucher_type)
            ->where(function ($q) use ($id) {
                $q->where('voucher_no', '=', $id);
            })
            ->get();
        $dr_id = $items[0]->id;
        $cr_id = $items[1]->id;
        $dr_items = $this->parentModel::find($dr_id);
        $dr_items->voucher_no = $id;
        $dr_items->branch_id = $request->branch_id;
        $dr_items->bank_cash_id = $request->bank_cash_id;
        $dr_items->dr = $request->amount;
        $dr_items->voucher_date = $voucher_date;
        $dr_items->particulars = $request->particulars;
        $dr_items->updated_by = auth()->user()->email;

        $cr_items = $this->parentModel::find($cr_id);
        $cr_items->voucher_no = $id;
        $cr_items->branch_id = $request->branch_id;
        $cr_items->bank_cash_id = $request->bank_cash_id_cr;
        $cr_items->cr = $request->amount;
        $cr_items->voucher_date = $voucher_date;
        $cr_items->particulars = $request->particulars;
        $cr_items->updated_by = auth()->user()->email;

        DB::beginTransaction();
        try {
            $dr_items->save();
            $cr_items->save();
            Session::flash('success', "Update Successfully");
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Session::flash('error', $e->getMessage());
        }
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
            'module_name' => 'Contra Voucher Report',
            'voucher_type' => 'CONTRA VOUCHER'
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
        Cache::forget('count_trashed_cnt');
        Cache::forget('count_cnt');
        Session::flash('success', "Successfully Trashed");
        return redirect()->back();
    }

    public function trashed()
    {
        $data = [];
        if (Cache::get('count_cnt') && Cache::get('count_cnt') != null) {
            $data['count_cnt'] = Cache::get('count_cnt');
        } else {
            $data['count_cnt'] = $this->parentModel::where('voucher_type', $this->voucher_type)->count();
            Cache::put('count_cnt', $data['count_cnt']);
        }
        $items = $this->parentModel::where('voucher_type', $this->voucher_type)
            ->onlyTrashed()
            ->orderBy('deleted_at', 'DESC')
            ->with('Branch', 'BankCash')
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
        Cache::forget('count_trashed_cnt');
        Cache::forget('count_cnt');
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
        Cache::forget('count_trashed_cnt');
        Cache::forget('count_cnt');
        Session::flash('success', 'Permanently Delete');
        return redirect()->back();
    }

    public function activeSearch(Request $request)
    {
        $data = [];
        if (Cache::get('count_trashed_cnt') && Cache::get('count_trashed_cnt') != null) {
            $data['count_trashed_cnt'] = Cache::get('count_trashed_cnt');
        } else {
            $data['count_trashed_cnt'] = $this->parentModel::onlyTrashed()->where('voucher_type', $this->voucher_type)->count();
            Cache::put('count_trashed_cnt', $data['count_trashed_cnt']);
        }
        $search = $request["search"];
        $items = $this->parentModel::where('voucher_type', '=', $this->voucher_type)
            ->where(function ($q) use ($search) {
                $q->where('voucher_no', '=', $search)
                    ->orWhere('voucher_date', 'like', date("Y-m-d", strtotime($search)))
                    ->orWhere('cheque_number', '=', $search)
                    ->orWhere('dr', '=', $search)
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
            ->with('Branch', 'BankCash')
            ->paginate(60);
        return view($this->parentView . '.index', $data)
            ->with('items', $items);
    }

    public function trashedSearch(Request $request)
    {
        $data = [];
        if (Cache::get('count_cnt') && Cache::get('count_cnt') != null) {
            $data['count_cnt'] = Cache::get('count_cnt');
        } else {
            $data['count_cnt'] = $this->parentModel::onlyTrashed()->where('voucher_type', $this->voucher_type)->count();
            Cache::put('count_cnt', $data['count_cnt']);
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
            ->with('Branch', 'BankCash')
            ->orderBy('created_at', 'dsc')
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
    #end
}
