<?php

namespace App\Http\Controllers;

use App\IncomeExpenseGroup;
use App\IncomeExpenseHead;
use App\IncomeExpenseType;
use Illuminate\Http\Request;

use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
class IncomeExpenseHeadController extends Controller
{
    //    Important properties
    public $parentModel = IncomeExpenseHead::class;
    public $parentRoute = 'income_expense_head';
    public $parentView = "admin.income-expense-head";

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [];
        if (Cache::get('total_trashed_income_expense_heads') && Cache::get('total_trashed_income_expense_heads') != null) {
            $data['total_trashed_income_expense_heads'] = Cache::get('total_trashed_income_expense_heads');
        } else {
            $data['total_trashed_income_expense_heads'] = $this->parentModel::onlyTrashed()->count();
            Cache::put('total_trashed_income_expense_heads', $data['total_trashed_income_expense_heads']);
        }
        $items = $this->parentModel::orderBy('created_at', 'desc')->with('IncomeExpenseType', 'IncomeExpenseGroup')->paginate(60);
        $data['shipments'] = DB::connection('mysql2')
        ->table('shipments')
        ->join('customers', 'shipments.sender_id', '=', 'customers.id')
        ->selectRaw(
            'MAX(shipments.id) as id, MAX(shipments.branch_id) as branch_id, 
             MAX(shipments.payment_method) as payment_method, MAX(shipments.balance) as balance, 
             customers.name as sender, MAX(shipments.sender_id) as sender_id'
        )
        
        ->groupBy('customers.name', 'shipments.sender_id')  // Added shipments.sender_id to GROUP BY
        ->get();
        return view($this->parentView . '.index', $data)->with('items', $items);
    }
    /**
     * This function return group & type data
     *
     * @author      Md. Al-Mahmud <mamun120520@gmail.com>
     * @version     1.0
     * @see         
     * @since       08/12/2022
     * Time         22:44:35
     * @param       
     * @return      $data
     */
    public function __get_cache_data()
    {
        # code...   
        $data = [];
        if (Cache::get('income_expense_groups') && Cache::get('income_expense_groups') != null) {
            $data['income_expense_groups'] = Cache::get('income_expense_groups');
        } else {
            $data['income_expense_groups'] = IncomeExpenseGroup::all();
            Cache::put('income_expense_groups', $data['income_expense_groups']);
        }
        if (Cache::get('income_expense_types') && Cache::get('income_expense_types') != null) {
            $data['income_expense_types'] = Cache::get('income_expense_types');
        } else {
            $data['income_expense_types'] = IncomeExpenseType::all();
            Cache::put('income_expense_types', $data['income_expense_types']);
        }
        return $data;
    }
    #end

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view($this->parentView . '.create', $this->__get_cache_data());
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
            'name' => 'required|string|unique:income_expense_heads',
            'income_expense_type_id' => 'required|numeric|min:1',
            'income_expense_group_id' => 'required|numeric|min:1',
        ]);
        $this->parentModel::create([
            'name' => $request->name,
            'unit' => $request->unit,
            'opening_balance'=>$request->opening_balance,
            'income_expense_type_id' => $request->income_expense_type_id,
            'income_expense_group_id' => $request->income_expense_group_id,
            'type' => $request->type,
            'created_by' => auth()->user()->email,
        ]);
        Cache::forget('income_expense_heads');
        Cache::forget('total_income_expense_heads');
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
        $item = $this->parentModel::find($request->id);
        if (empty($item)) {
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
        $items = $this->parentModel::find($id);
        if (empty($items)) {
            Session::flash('error', "Item not found");
            return redirect()->back();
        }
        return view($this->parentView . '.edit', $this->__get_cache_data())->with('item', $items);
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
            'name' => 'sometimes|string|unique:income_expense_heads,name,' . $id,
            'income_expense_type_id' => 'required|numeric|min:1',
            'income_expense_group_id' => 'required|numeric|min:1',
        ]);
        $items = $this->parentModel::find($id);
        $items->name = $request->name;
        $items->unit = $request->unit;
        $items->opening_balance = $request->opening_balance;
        $items->income_expense_type_id = $request->income_expense_type_id;
        $items->income_expense_group_id = $request->income_expense_group_id;
        $items->type = $request->type;
        $items->updated_by = auth()->user()->email;
        $items->save();
        Cache::forget('income_expense_heads');
        Session::flash('success', "Update Successfully");
        return redirect()->route($this->parentRoute);
    }

    public function pdf(Request $request)
    {
        $item = $this->parentModel::find($request->id);
        if (empty($item)) {
            Session::flash('error', "Item not found");
            return redirect()->back();
        }
        $now = new \DateTime();
        $date = $now->format(Config('settings.date_format') . ' h:i:s');
        $extra = array(
            'current_date_time' => $date,
            'module_name' => 'Ledger Name'
        );
        $pdf = PDF::loadView($this->parentView . '.pdf', ['items' => $item, 'extra' => $extra])->setPaper('a4', 'landscape');
        //return $pdf->stream('invoice.pdf');
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
        $items = $this->parentModel::find($id);
        if (empty($items)) {
            Session::flash('error', "Item not found");
            return redirect()->back();
        }
        if (count($this->parentModel::find($id)->Transaction) > 0) {
            Session::flash('error', "You can not delete it.Because it has Some Transaction");
            return redirect()->back();
        }
        try {
            $items->deleted_by = auth()->user()->email;
            $items->name = $items->id . '_' . $items->name;
            $items->save();
            $items->delete();
            Cache::forget('income_expense_heads');
            Session::flash('success', "Successfully Trashed");
        } catch (\Exception $e) {
            Session::flash('error', $e->getMessage());
        }
        return redirect()->back();
    }
    public function trashed()
    {
        $data = [];
        if (Cache::get('total_income_expense_heads') && Cache::get('total_income_expense_heads') != null) {
            $data['total_income_expense_heads'] = Cache::get('total_income_expense_heads');
        } else {
            $data['total_income_expense_heads'] = $this->parentModel::count();
            Cache::put('total_income_expense_heads', $data['total_income_expense_heads']);
        }
        $items = $this->parentModel::onlyTrashed()->with('IncomeExpenseType', 'IncomeExpenseGroup')->paginate(60);
        return view($this->parentView . '.trashed', $data)->with("items", $items);
    }

    public function restore($id)
    {
        $items = $this->parentModel::onlyTrashed()->where('id', $id)->first();
        $items->restore();
        $items->updated_by = auth()->user()->email;
        $items->save();
        Cache::forget('total_income_expense_heads');
        Cache::forget('total_trashed_income_expense_heads');
        Cache::forget('income_expense_heads');
        Session::flash('success', 'Successfully Restore');
        return redirect()->back();
    }
    public function kill($id)
    {
        $items = $this->parentModel::withTrashed()->where('id', $id)->first();
        if (count($this->parentModel::withTrashed()->find($id)->Transaction) > 0) {
            Session::flash('error', "You can not delete it.Because it has Some Transaction");
            return redirect()->back();
        }
        try {
            Cache::forget('total_income_expense_heads');
            Cache::forget('total_trashed_income_expense_heads');
            $items->forceDelete();
            Cache::forget('income_expense_heads');
            Session::flash('success', 'Permanently Delete');
        } catch (\Exception $e) {
            Session::flash('error', $e->getMessage());
        }
        return redirect()->back();
    }
public function activeSearch(Request $request)
    {
        // Validate search input
        $request->validate([
            'search' => 'min:1'
        ]);
    
        $data = [];
    
        // Cache income and expense heads data
        if (Cache::get('total_income_expense_heads') && Cache::get('total_income_expense_heads') != null) {
            $data['total_income_expense_heads'] = Cache::get('total_income_expense_heads');
        } else {
            $data['total_income_expense_heads'] = $this->parentModel::count();
            Cache::put('total_income_expense_heads', $data['total_income_expense_heads']);
        }
    
        if (Cache::get('total_trashed_income_expense_heads') && Cache::get('total_trashed_income_expense_heads') != null) {
            $data['total_trashed_income_expense_heads'] = Cache::get('total_trashed_income_expense_heads');
        } else {
            $data['total_trashed_income_expense_heads'] = $this->parentModel::onlyTrashed()->count();
            Cache::put('total_trashed_income_expense_heads', $data['total_trashed_income_expense_heads']);
        }
    
        $search = $request->input('search');
    
        // Search the main database (first connection)
        $items = $this->parentModel::where('name', 'like', '%' . $search . '%')
            ->orWhereHas('IncomeExpenseType', function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            })
            ->orWhereHas('IncomeExpenseGroup', function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            })
            ->orWhere('unit', 'like', '%' . $search . '%')
            ->with('IncomeExpenseType', 'IncomeExpenseGroup')
            ->paginate(60);
    
        // Search the second database connection
     $shipments = DB::connection('mysql2')
    ->table('shipments')
    ->join('customers', 'shipments.sender_id', '=', 'customers.id')
    ->selectRaw(
        'MAX(shipments.id) as id, 
         MAX(shipments.branch_id) as branch_id, 
         MAX(shipments.payment_method) as payment_method, 
         MAX(shipments.balance) as balance, 
         customers.name as sender, 
         shipments.sender_id'
    )
    ->where('customers.name', 'like', '%' . $search . '%')
    ->groupBy('shipments.sender_id', 'customers.name') // Group by sender ID and name
    ->get();
         
    
        // Pass both variables to the view
        $data['items'] = $items;       // Data from the main connection
    $data['shipments'] = $shipments; // Data from the second connection
    
        return view($this->parentView . '.index', $data);
    }
    
    public function trashedSearch(Request $request)
    {
        $request->validate([
            'search' => 'min:1'
        ]);
        $data = [];
        if (Cache::get('total_income_expense_heads') && Cache::get('total_income_expense_heads') != null) {
            $data['total_income_expense_heads'] = Cache::get('total_income_expense_heads');
        } else {
            $data['total_income_expense_heads'] = $this->parentModel::count();
            Cache::put('total_income_expense_heads', $data['total_income_expense_heads']);
        }
        if (Cache::get('total_trashed_income_expense_heads') && Cache::get('total_trashed_income_expense_heads') != null) {
            $data['total_trashed_income_expense_heads'] = Cache::get('total_trashed_income_expense_heads');
        } else {
            $data['total_trashed_income_expense_heads'] = $this->parentModel::onlyTrashed()->count();
            Cache::put('total_trashed_income_expense_heads', $data['total_trashed_income_expense_heads']);
        }
        $search = $request["search"];
        $items = $this->parentModel::where('name', 'like', '%' . $search . '%')
            ->onlyTrashed()
            ->orWhereHas('IncomeExpenseType', function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            })
            ->onlyTrashed()
            ->orWhereHas('IncomeExpenseGroup', function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            })
            ->orWhere('unit', 'like', '%' . $search . '%')
            ->onlyTrashed()
            ->with('IncomeExpenseType', 'IncomeExpenseGroup')
            ->paginate(60);
        return view($this->parentView . '.trashed',  $data)
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
                $this->destroy($id);
            }
            return redirect()->back();
        } elseif ($request->apply_comand_top == 2 || $request->apply_comand_bottom == 2) {
            foreach ($request->items["id"] as $id) {
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
                $this->restore($id);
            }
        } elseif ($request->apply_comand_top == 2 || $request->apply_comand_bottom == 2) {
            foreach ($request->items["id"] as $id) {
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
