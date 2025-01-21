<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
  public function index(Request $request)
    {
        $query = \DB::connection('mysql2')->table('shipments')
            ->join('branches', 'shipments.branch_id', '=', 'branches.id')
            ->select('shipments.*', 'branches.name')
            ->orderBy('shipments.created_at', 'desc'); // Order by your desired column

    $items = $query->paginate(10); // 10 is the number of items per page
   // $branch = \DB::connection('mysql2')->table('branches');

        // Additional variables for the view
        $data['count_trashed_dv'] = 0; // Set this according to your application's logic
        return view('admin.invoice.index', $data, compact('items'));
    }
   public function search(Request $request)
    {
        $query = $request->input('search');
    
        $items = DB::connection('mysql2')
        ->table('shipments')
        ->join('branches', 'shipments.branch_id', '=', 'branches.id')
        ->where('shipments.booking_number', 'LIKE', '%' . $query . '%')
        ->orWhere('shipments.shiping_method', 'LIKE', '%' . $query . '%')
       
        ->orWhere('branches.name', 'LIKE', '%' . $query . '%') // Search by branch name
        ->select('shipments.*', 'branches.name') // Select needed fields
        ->paginate(10); // Use paginate for pagination
        $count_trashed_dv = 0; // Adjust according to your application logic
    
        return view('admin.invoice.index', compact('items', 'count_trashed_dv'));
    }
public function action(Request $request)
{
    // Handle the action logic here
    // For example, delete selected invoices, etc.
     // Apply date filter if provided
     $query =\DB::connection('mysql2')->table('shipments')
            ->join('branches', 'shipments.branch_id', '=', 'branches.id')
            ->select('shipments.*', 'branches.name')
            ->orderBy('shipments.created_at', 'desc');

     $startDate = $request->input('start_date');
     $endDate = $request->input('end_date');

     if ($startDate && $endDate) {
         // Convert input dates to the correct format (Y-m-d)
         $startDate = date('Y-m-d', strtotime($startDate));
         $endDate = date('Y-m-d', strtotime($endDate));

         // Add time to end date to include the whole day
         $endDate = $endDate . ' 23:59:59';

         $query->whereBetween('created_date', [$startDate . ' 00:00:00', $endDate]);
     }

     $items = $query->paginate(10); // 10 is the number of items per page

     // Additional variables for the view
     $data['count_trashed_dv'] = 0; // Set this according to your application's logic
     return view('admin.invoice.index', $data, compact('items'));

    //return redirect()->route('invoice')->with('success', 'Action applied successfully');
}
}
