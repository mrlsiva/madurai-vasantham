<?php

namespace App\Http\Controllers\ChitFund;
use App\Http\Controllers\Controller;

use App\Imports\ChitFundImportUsers;
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
}
