<?php

namespace App\Http\Controllers;

use App\BankCash;
use Illuminate\Http\Request;

use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

class BankCashController extends Controller
{

    //    Important properties
    public $parentModel = BankCash::class;
    public $parentRoute = 'bank_cash';
    public $parentView = "admin.bank-cash";

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [];
        if (Cache::get('total_trashed_bank_cashes') && Cache::get('total_trashed_bank_cashes') != null) {
            $data['total_trashed_bank_cashes'] = Cache::get('total_trashed_bank_cashes');
        } else {
            $data['total_trashed_bank_cashes'] = $this->parentModel::onlyTrashed()->count();
            Cache::put('total_trashed_bank_cashes', $data['total_trashed_bank_cashes']);
        }

        $items = $this->parentModel::orderBy('created_at', 'desc')->paginate(60);
        return view($this->parentView . '.index', $data)->with('items', $items);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view($this->parentView . '.create');
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
            'name' => 'required|string|unique:bank_cashes',
        ]);
        $this->parentModel::create([
            'name' => $request->name,
            'account_number' => $request->account_number,
             'opening_balance'=>$request->opening_balance,
            'type'=>$request->type,
            'description' => $request->description,
            'created_by' => auth()->user()->email,
        ]);
        Session::flash('success', "Successfully  Create");
        Cache::forget('total_bank_cashes');
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
        return view($this->parentView . '.edit')->with('item', $items);
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
            'name' => 'sometimes|string|unique:bank_cashes,name,' . $id,
        ]);
        $items = $this->parentModel::find($id);
        $items->name = $request->name;
        $items->account_number = $request->account_number;
         $items->opening_balance = $request->opening_balance;
        $items->type=$request->type;
        $items->description = $request->description;
        $items->updated_by = auth()->user()->email;
        $items->save();
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
            'module_name' => 'Bank Cash'
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
        if (count($this->parentModel::find($id)->Transactions) > 0) {
            Session::flash('error', "You can not delete it. Because it has some transaction.");
            return redirect()->back();
        }

        $items->deleted_by = auth()->user()->email;
        $items->name = $items->id . '_' . $items->name;
        $items->save();
        try {
            $items->delete();
            Session::flash('success', "Successfully Trashed");
            Cache::forget('total_bank_cashes');
        } catch (\Exception $e) {
            Session::flash('error', $e->getMessage());
        }
        return redirect()->back();
    }

    public function trashed()
    {
        $data = [];
        if (Cache::get('total_bank_cashes') && Cache::get('total_bank_cashes') != null) {
            $data['total_bank_cashes'] = Cache::get('total_bank_cashes');
        } else {
            $data['total_bank_cashes'] = $this->parentModel::count();
            Cache::put('total_bank_cashes', $data['total_bank_cashes']);
        }
        $items = $this->parentModel::onlyTrashed()->paginate(60);
        return view($this->parentView . '.trashed', $data)->with("items", $items);
    }

    public function restore($id)
    {
        $items = $this->parentModel::onlyTrashed()->where('id', $id)->first();
        try {
            $items->restore();
            $items->updated_by = auth()->user()->email;
            $items->save();
            Session::flash('success', 'Successfully Restore');
            Cache::forget('total_bank_cashes');
        } catch (\Exception $e) {
            Session::flash('error', $e->getMessage());
        }
        return redirect()->back();
    }

    public function kill($id)
    {
        $items = $this->parentModel::withTrashed()->where('id', $id)->first();
        if (count($this->parentModel::withTrashed()->find($id)->Transactions) > 0) {
            Session::flash('error', "You can not permanently delete it. Because it has some transaction.");
            return redirect()->back();
        }
        try {
            $items->forceDelete();
            Session::flash('success', 'Permanently Delete');
            Cache::forget('total_bank_cashes');
            return redirect()->back();
        } catch (\Exception $e) {
            Session::flash('error', $e->getMessage());
        }
    }

    public function activeSearch(Request $request)
    {
        $request->validate([
            'search' => 'min:1'
        ]);
        $data = [];
        if (Cache::get('total_bank_cashes') && Cache::get('total_bank_cashes') != null) {
            $data['total_bank_cashes'] = Cache::get('total_bank_cashes');
        } else {
            $data['total_bank_cashes'] = $this->parentModel::onlyTrashed()->count();
            Cache::put('total_bank_cashes', $data['total_bank_cashes']);
        }
        $search = $request["search"];
        $items = $this->parentModel::where('name', 'like', '%' . $search . '%')
            ->orWhere('account_number', 'like', '%' . $search . '%')
            ->orWhere('description', 'like', '%' . $search . '%')
            ->paginate(60);
        return view($this->parentView . '.index', $data)
            ->with('items', $items);
    }

    public function trashedSearch(Request $request)
    {
        $request->validate([
            'search' => 'min:1'
        ]);
        $data = [];
        if (Cache::get('total_bank_cashes') && Cache::get('total_bank_cashes') != null) {
            $data['total_bank_cashes'] = Cache::get('total_bank_cashes');
        } else {
            $data['total_bank_cashes'] = $this->parentModel::count();
            Cache::put('total_bank_cashes', $data['total_bank_cashes']);
        }
        $search = $request["search"];
        $items = $this->parentModel::where('name', 'like', '%' . $search . '%')
            ->onlyTrashed()
            ->orWhere('account_number', 'like', '%' . $search . '%')
            ->onlyTrashed()
            ->orWhere('description', 'like', '%' . $search . '%')
            ->onlyTrashed()
            ->paginate(60);

        return view($this->parentView . '.trashed')
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
