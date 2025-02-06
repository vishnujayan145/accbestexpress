<?php

namespace App\Http\Controllers;

use App\BankCash;
use App\Branch;
use App\Exports\Ledger\BranchWiseLedger;
use App\Exports\Ledger\BankCashWise;
use App\Exports\Ledger\IncomeExpenseHeadWise;
use App\Helpers\Helper;
use App\IncomeExpenseHead;
use App\Transaction;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;
use Maatwebsite\Excel\Facades\Excel;
use App\Invoiceuse;
use App\Voucher;
use App\Invoice;
use App\Receivable;
use App\DeliveryVoucher;
use App\InvoiceDetail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class DeliveryVoucherController extends Controller
{
    public $parentView = "admin.delivery_voucher";

    public function index()
    {
        $data['bank_cashes'] = BankCash::all();

        $lastVoucher = DeliveryVoucher::latest()->first();
        $data['next_voucher_no'] = $lastVoucher ? $lastVoucher->voucher_id + 1 : 1;

        $data['income_expense_heads'] = IncomeExpenseHead::all();

        return view('admin.delivery_voucher.index', $data);
    }

 
    public function store(Request $request)
    {
        // Debugging - Log the received request data
        Log::info('Received Data:', $request->all());

        // Validate input fields
        $validator = Validator::make($request->all(), [
            'voucher_id' => 'required|string',
            'ship_no' => 'required|string',
            'party_id' => 'required|integer',
            'date' => 'required|date',
            'pcs' => 'required|array',
            'weight' => 'required|array',
            'rate' => 'required|array',
            'amt_clring' => 'required|array',
            'duty' => 'required|array',
            'total' => 'required|array',
        ]);

        if ($validator->fails()) {
            Log::error('Validation Failed:', $validator->errors()->toArray());
            return response()->json(['error' => 'Please Enter Fields', 'details' => $validator->errors()], 422);
        }

        try {
            // Insert into delivery_vouchers table
            $voucher = DeliveryVoucher::create([
                'voucher_id' => $request->voucher_id,
                'ship_no' => $request->ship_no,
                'party_id' => $request->party_id,
                'date' => $request->date,
                'remarks' => $request->remarks,
            ]);

            foreach (array_slice($request->pcs, 1) as $index => $pcs) {
                $realIndex = $index + 1; // Adjust index to match original array
                InvoiceDetail::create([
                    'voucher_id' => $voucher->id,
                    'pcs' => $pcs,
                    'weight' => $request->weight[$realIndex],
                    'rate' => $request->rate[$realIndex],
                    'amt_clring' => $request->amt_clring[$realIndex],
                    'duty' => $request->duty[$realIndex],
                    'total' => $request->total[$realIndex],
                ]);
            }


            return response()->json(['success' => 'Voucher and invoices added successfully!'], 200);
        } catch (\Exception $e) {
            Log::error('Error storing data:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Database error: ' . $e->getMessage()], 500);
        }
    }

    
    public function all(Request $request)
    {
        $data = [];
    
        if (Cache::has('count_trashed_cv')) {
            $data['count_trashed_cv'] = Cache::get('count_trashed_cv');
        }
    
        // Get search query
        $search = $request->input('search');
    
        // Fetch data with a left join
        $query = DeliveryVoucher::leftJoin('invoice_details', 'delivery_vouchers.voucher_id', '=', 'invoice_details.voucher_id')
            ->leftJoin('income_expense_heads', 'delivery_vouchers.party_id', '=', 'income_expense_heads.id') // Join to get name
            ->select(
                'delivery_vouchers.id',
                'delivery_vouchers.voucher_id',
                'delivery_vouchers.ship_no',
                'delivery_vouchers.date',
                'delivery_vouchers.party_id',
                'delivery_vouchers.remarks',
                'income_expense_heads.name AS party_name', // Selecting name
                DB::raw('SUM(invoice_details.total) AS total_amount') // Sum total amounts if multiple invoices exist
            )
            ->groupBy('delivery_vouchers.voucher_id', 'delivery_vouchers.ship_no', 'delivery_vouchers.date', 'delivery_vouchers.party_id', 'delivery_vouchers.remarks', 'income_expense_heads.name', 'delivery_vouchers.id')
            ->orderBy('delivery_vouchers.id', 'desc');
    
        // Apply search filter
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('delivery_vouchers.ship_no', 'LIKE', "%{$search}%")
                  ->orWhere('delivery_vouchers.date', 'LIKE', "%{$search}%")
                  ->orWhere('income_expense_heads.name', 'LIKE', "%{$search}%"); // Searching by Party Name
            });
        }
    
        $items = $query->paginate(60);
    
        return view($this->parentView . '.all', $data)->with('items', $items)->with('search', $search);
    }
    
    public function edit($id)
    {
        // Fetch the delivery voucher details using the given ID
        $delivery_voucher = DeliveryVoucher::findOrFail($id);
    
        // Debugging: Log the voucher_id to ensure we have the correct ID
        \Log::info('Delivery Voucher ID:', ['voucher_id' => $delivery_voucher->id]);
    
        // Fetch all invoice details where invoice_details.voucher_id matches delivery_vouchers.id
        $invoice_details = InvoiceDetail::where('voucher_id', $delivery_voucher->id)->get();
    
        // Debugging: Log the fetched invoice details
        \Log::info('Fetched Invoice Details:', $invoice_details->toArray());
    
        // Fetch income/expense heads for dropdown
        $income_expense_heads = IncomeExpenseHead::all();
    
        return view('admin.delivery_voucher.edit', compact('delivery_voucher', 'invoice_details', 'income_expense_heads'));
    }
    
    
    public function update(Request $request, $id)
    {
        // Debugging - Log the received request data
        Log::info('Received Data for Update:', $request->all());
    
        // Validate input fields
        $validator = Validator::make($request->all(), [
            'voucher_id' => 'required|string',
            'ship_no' => 'required|string',
            'party_id' => 'required|integer',
            'date' => 'required|date',
            'pcs' => 'required|array',
            'weight' => 'required|array',
            'rate' => 'required|array',
            'amt_clring' => 'required|array',
            'duty' => 'required|array',
            'total' => 'required|array',
        ]);
    
        if ($validator->fails()) {
            Log::error('Validation Failed:', $validator->errors()->toArray());
            return response()->json(['error' => 'Please Enter Fields', 'details' => $validator->errors()], 422);
        }
    
        try {
            // Find the existing voucher
            $voucher = DeliveryVoucher::findOrFail($id);
    
            // Update the voucher details
            $voucher->update([
                'voucher_id' => $request->voucher_id,
                'ship_no' => $request->ship_no,
                'party_id' => $request->party_id,
                'date' => $request->date,
                'remarks' => $request->remarks,
            ]);
    
            // Update or insert invoice details
            foreach ($request->pcs as $index => $pcs) {
                $invoiceData = [
                    'voucher_id' => $voucher->id,
                    'pcs' => $pcs,
                    'weight' => $request->weight[$index],
                    'rate' => $request->rate[$index],
                    'amt_clring' => $request->amt_clring[$index],
                    'duty' => $request->duty[$index],
                    'total' => $request->total[$index],
                ];
    
                // Check if the invoice exists for this voucher_id
                $invoice = InvoiceDetail::where('voucher_id', $voucher->id)->skip($index)->first();
    
                if ($invoice) {
                    // Update existing invoice
                    $invoice->update($invoiceData);
                } else {
                    // Insert new invoice record
                    InvoiceDetail::create($invoiceData);
                }
            }
    
            return redirect()->route('delivery_voucher.all')->with('success', 'Voucher updated successfully!');
        } catch (\Exception $e) {
            Log::error('Error updating data:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Database error: ' . $e->getMessage()], 500);
        }
    }
    
    public function delete($id)
    {
        // Delete related invoice_details first (to avoid foreign key constraints)
        DB::table('invoice_details')->where('voucher_id', $id)->delete();
    
        // Delete from delivery_vouchers
        DB::table('delivery_vouchers')->where('id', $id)->delete();
    
        return redirect()->back()->with('success', 'Record deleted successfully');
    }
    
    
    private function generateNextVoucherNo()
    {
       $lastVoucher = DeliveryReportVhr::latest()->first();
        return $lastVoucher ? $lastVoucher->voucher_id + 1 : 1;
    }
}
