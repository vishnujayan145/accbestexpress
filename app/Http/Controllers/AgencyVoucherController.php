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
use App\Receivable; // Import the Receivable model


use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
class AgencyVoucherController extends Controller
{
    public $parentModel = Transaction::class;
    public $parentRoute = 'agency_voucher';
    public $parentView = "admin.agency_voucher";
    public function index()
    {
        $data['bank_cashes'] = BankCash::all();
        $crvoucher = new CrVoucherController();
        // Fetch the last voucher and calculate the next voucher_no
    $lastVoucher = Voucher::orderBy('voucher_id', 'desc')->first();
    $data['next_voucher_no'] = $lastVoucher ? $lastVoucher->voucher_id + 1 : 1; // If no vouchers exist, start with 1

        return view('admin.agency_voucher.index',$data, $crvoucher->__getBranchBankCashIncomeExpenseHead());
    }
   
public function saveVoucherAndInvoices(Request $request)
{
    $data = $request->all();

    // Calculate the total amount from the invoices
    $totalAmount = 0;
    foreach ($data['invoices'] as $invoiceData) {
        $totalAmount += $invoiceData['amount'];
    }

    // Fetch the branch name and head of account name using the respective IDs
    $branch = Branch::find($data['branch']);
    $branchName = $branch ? $branch->name : null;

    $headOfAccount = IncomeExpenseHead::find($data['head_of_account']);
    $headOfAccountName = $headOfAccount ? $headOfAccount->name : null;

    // Save voucher details with the total amount
    $voucher = new Voucher();
    $voucher->voucher_id = $data['voucher_id'];
    $voucher->date = $data['date'];
    $voucher->branch = $data['branch'];
    $voucher->bank_name = "not updated";//$data['bank_name'];
    $voucher->head_of_account = $data['head_of_account'];
    $voucher->description = $data['description'];
    $voucher->total_amount = $totalAmount; // Save the total amount
    $voucher->save();

    // Save invoices
    foreach ($data['invoices'] as $invoiceData) {
        $invoice = new Invoice();
        $invoice->voucher_no = $voucher->voucher_id;
        $invoice->invoice_no = $invoiceData['invoice_no'];
        $invoice->pcs = $invoiceData['pcs'];
        $invoice->weight = $invoiceData['weight'];
        $invoice->destination = $invoiceData['destination'];
        $invoice->rate = $invoiceData['rate'];
        $invoice->duty = $invoiceData['duty'];
        $invoice->amount = $invoiceData['amount'];
        $invoice->save();
    }
     // Save receivables
     $receivable = new Receivable();
     $receivable->voucher_no = $voucher->id;
     $receivable->date = $voucher->date;
     $receivable->branch_name = $branchName;
     $receivable->head_of_account_name = $headOfAccountName;
     $receivable->total_amount = $totalAmount;
     $receivable->save();

    return response()->json(['message' => 'Voucher and invoices saved successfully!']);
}
public function all()
    {
        $data = [];
        if (Cache::get('count_trashed_cv') && Cache::get('count_trashed_cv') != null) {
            $data['count_trashed_cv'] = Cache::get('count_trashed_cv');
        }
        $items = Receivable::with('Branch', 'IncomeExpenseHead', 'BankCash')
            ->orderBy('id', 'desc')
            ->paginate(60);
        return view($this->parentView . '.all', $data)->with('items', $items);
    }
}