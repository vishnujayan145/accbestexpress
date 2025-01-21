<?php

namespace App\Http\Controllers;


use App\Transaction;
use Illuminate\Http\Request;

use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

class JournalVoucherController extends Controller
{

    //    Important properties
    public $parentModel = Transaction::class;
    public $parentRoute = 'jnl_voucher';
    public $parentView = "admin.jnl-voucher";

    public $voucher_type = "JV";

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [];
        if (Cache::get('count_trashed_jv') && Cache::get('count_trashed_jv') != null) {
            $data['count_trashed_jv'] = Cache::get('count_trashed_jv');
        } else {
            $data['count_trashed_jv'] = $this->parentModel::onlyTrashed()->where('voucher_type', $this->voucher_type)->count();
            Cache::put('count_trashed_jv', $data['count_trashed_jv']);
        }
        $items = $this->parentModel::where('voucher_type', $this->voucher_type)
            ->with('Branch', 'IncomeExpenseHead')
            ->orderBy('voucher_no', 'DESC')
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
        $cr_crontroller = new CrVoucherController();
        return view($this->parentView . '.create', $cr_crontroller->__getBranchBankCashIncomeExpenseHead());
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
            'branch_id_cr' => 'required',
            'voucher_date' => 'required',
        ]);
        $date = new \DateTime($request->voucher_date);
        $voucher_date = $date->format('Y-m-d'); // 31-07-2012 '2008-11-11'
        $reference_no = $request->reference_no;

        $voucher_info = $this->parentModel::where('voucher_type', $this->voucher_type)
            ->withTrashed()
            ->orderBy('voucher_no', 'DESC')
            ->first();
        if (!empty($voucher_info)) {
            $voucher_no = $voucher_info->voucher_no + 1;
        } else {
            $voucher_no = 1;
        }
        DB::beginTransaction();
        try {
            foreach ($request->income_expense_head_id as $key => $id) {
                if ($id == 0) {
                    continue;
                }
                $this->parentModel::create([
                    'voucher_no' => $voucher_no,
                    'branch_id' => $request->branch_id,
                    'voucher_type' => $this->voucher_type,
                    'voucher_date' => $voucher_date,
                    'particulars' => $request->particulars,
                    'reference_no' =>$reference_no,
                    'income_expense_head_id' => $id,
                    'dr' => $request->amount[$key],
                    'created_by' => auth()->user()->email,
                ]);
            }
            foreach ($request->income_expense_head_id_cr as $key => $id) {
                if ($id == 0) {
                    continue;
                }
                $this->parentModel::create([
                    'voucher_no' => $voucher_no,
                    'branch_id' => $request->branch_id_cr,
                    'voucher_type' => $this->voucher_type,
                    'voucher_date' => $voucher_date,
                    'particulars' => $request->particulars,
                    'reference_no' =>$reference_no,
                    'income_expense_head_id' => $id,
                    'cr' => $request->amount_cr[$key],
                    'created_by' => auth()->user()->email,
                ]);
            }
            DB::commit();
            Session::flash('success', "Successfully  Create");
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
        if (count($item) == 0) {
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
        $DrItems = array();
        $CrItems = array();
        foreach ($items as $item) {
            if ($item->dr > 0) {
                $DrItems[] = $item;
            } else {
                $CrItems[] = $item;
            }
        }
        $DrCrItems = array('dr' => $DrItems, 'cr' => $CrItems);
        $cr_crontroller = new CrVoucherController();
        return view($this->parentView . '.edit', $cr_crontroller->__getBranchBankCashIncomeExpenseHead())->with('items', $DrCrItems);
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
            'branch_id_cr' => 'required',
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
        $created_at = $items[0]->created_at;
        DB::beginTransaction();
        try {
            $this->kill($id); /// Old Item kill then new items created
            foreach ($request->income_expense_head_id as $key => $income_expense_head_id) {
                if ($income_expense_head_id == 0) {
                    continue;
                }
                $d_model = $this->parentModel::create([
                    'voucher_no' => $id,
                    'branch_id' => $request->branch_id,
                    'voucher_type' => $this->voucher_type,
                    'voucher_date' => $voucher_date,
                    'particulars' => $request->particulars,
                    'income_expense_head_id' => $income_expense_head_id,
                    'dr' => $request->amount[$key],

                    'created_by' => $created_by,
                    'updated_by' => auth()->user()->email,
                ]);
                $d_model->created_at = $created_at;
                $d_model->save();
            }
            foreach ($request->income_expense_head_id_cr as $key => $income_expense_head_id_cr) {
                if ($income_expense_head_id_cr == 0) {
                    continue;
                }
                $c_model = $this->parentModel::create([
                    'voucher_no' => $id,
                    'branch_id' => $request->branch_id_cr,
                    'voucher_type' => $this->voucher_type,
                    'voucher_date' => $voucher_date,
                    'particulars' => $request->particulars,
                    'income_expense_head_id' => $income_expense_head_id_cr,
                    'cr' => $request->amount_cr[$key],
                    'created_by' => $created_by,
                    'updated_by' => auth()->user()->email,
                ]);
                $c_model->created_at = $created_at;
                $c_model->save();
            }
            Session::flash('success', "Update Successfully");
            DB::commit();
        } catch (\Exception $e) {
            Session::flash('error', $e->getMessage());
            DB::rollBack();
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
            'module_name' => 'Journal Voucher Report',
            'voucher_type' => 'JOURNAL VOUCHER'
        );
        // return view('admin.dr-voucher.pdf');

        $pdf = PDF::loadView($this->parentView . '.pdf', ['items' => $item, 'extra' => $extra])->setPaper('a4', 'landscape');

        //return $pdf->stream($extra['current_date_time'] . '_' . $extra['module_name'] . '.pdf');
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
        DB::beginTransaction();
        try {
            foreach ($items as $item) {
                $item->delete();
            }
            Session::flash('success', "Successfully Trashed");
            DB::commit();
            Cache::forget('count_trashed_jv');
            Cache::forget('count_jv');
        } catch (\Exception $e) {
            DB::rollBack();
            Session::flash('error', $e->getMessage());
        }
        return redirect()->back();
    }

    public function trashed()
    {
        $data = [];
        if (Cache::get('count_jv') && Cache::get('count_jv') != null) {
            $data['count_jv'] = Cache::get('count_jv');
        } else {
            $data['count_jv'] = $this->parentModel::onlyTrashed()->where('voucher_type', $this->voucher_type)->count();
            Cache::put('count_jv', $data['count_jv']);
        }
        $items = $this->parentModel::where('voucher_type', $this->voucher_type)
            ->onlyTrashed()
            ->with('Branch', 'IncomeExpenseHead')
            ->orderBy('deleted_at', 'DESC')
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
        DB::beginTransaction();
        try {
            foreach ($items as $item) {
                $item->restore();
                $item->updated_by = auth()->user()->email;
                $item->save();
            }
            Cache::forget('count_trashed_jv');
            Cache::forget('count_jv');
            Session::flash('success', 'Successfully Restore');
            DB::commit();
        } catch (\Exception $e) {
            Session::flash('error', $e->getMessage());
            DB::rollBack();
        }
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
        DB::beginTransaction();
        try {
            foreach ($items as $item) {
                $item->forceDelete();
            }
            Cache::forget('count_trashed_jv');
            Cache::forget('count_jv');
            Session::flash('success', 'Permanently Delete');
            DB::commit();
        } catch (\Exception $e) {
            Session::flash('error', $e->getMessage());
            DB::rollBack();
        }
        return redirect()->back();
    }

    public function activeSearch(Request $request)
    {
        $data = [];
        if (Cache::get('count_trashed_jv') && Cache::get('count_trashed_jv') != null) {
            $data['count_trashed_jv'] = Cache::get('count_trashed_jv');
        } else {
            $data['count_trashed_jv'] = $this->parentModel::onlyTrashed()->where('voucher_type', $this->voucher_type)->count();
            Cache::put('count_trashed_jv', $data['count_trashed_jv']);
        }
        $search = $request["search"];
        $items = $this->parentModel::where('voucher_type', '=', $this->voucher_type)
            ->where(function ($q) use ($search) {
                $q->where('voucher_no', '=', $search)
                    ->orWhere('voucher_date', 'like', date("Y-m-d", strtotime($search)))
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
            ->with('Branch', 'IncomeExpenseHead')
            ->paginate(60);

        return view($this->parentView . '.index', $data)
            ->with('items', $items);
    }

    public function trashedSearch(Request $request)
    {
        $data = [];
        if (Cache::get('count_jv') && Cache::get('count_jv') != null) {
            $data['count_jv'] = Cache::get('count_jv');
        } else {
            $data['count_jv'] = $this->parentModel::onlyTrashed()->where('voucher_type', $this->voucher_type)->count();
            Cache::put('count_jv', $data['count_jv']);
        }
        $search = $request["search"];
        $items = $this->parentModel::where('voucher_type', '=', $this->voucher_type)
            ->onlyTrashed()
            ->where(function ($q) use ($search) {
                $q->where('voucher_no', '=', $search)
                    ->orWhere('voucher_date', 'like', date("Y-m-d", strtotime($search)))
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
            ->with('Branch', 'IncomeExpenseHead')
            ->orderBy('created_at', 'DESC')
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
}
