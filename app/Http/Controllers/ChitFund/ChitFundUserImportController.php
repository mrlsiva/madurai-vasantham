<?php

namespace App\Http\Controllers\ChitFund;
use App\Http\Controllers\Controller;

use App\Imports\ChitFundImportUsers;
use App\Imports\ChitFundBulkImportUsers;
use Maatwebsite\Excel\Facades\Excel;

use Illuminate\Http\Request;
use Validator;

class ChitFundUserImportController extends Controller
{
    public function chitfundImportIndex(Request $request){
        return view('admin.chitfund.import-user');
    }
    
    
    public function chitfundImportExcel(Request $request)
    { 
        // Validate the uploaded file
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        // Get the uploaded file
        $file = $request->file('file');

        // Process the Excel file
        Excel::import(new ChitFundImportUsers, $file);

        return redirect()->back()->with('success', 'Excel file imported successfully!');
    } 

    public function bulkUpload(Request $request)
    {
        // Validate the uploaded file
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
            'plan_id' => 'required'
        ]);

        // Get the uploaded file
        $file = $request->file('file');

        // Process the Excel file
        Excel::import(new ChitFundBulkImportUsers, $file);

        if (!empty(ChitFundBulkImportUsers::$failedRows)) {
        return back()->with([
            'success' => 'File imported with some skipped rows.',
            'failedRows' => ChitFundBulkImportUsers::$failedRows
        ]);
    }
    
        return redirect()->back()->with('success', 'Excel file imported successfully!');
    }   
}
