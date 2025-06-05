<?php

namespace App\Imports;

use App\Models\ChitFund\ChitFund_Scheme; 
use App\Models\ChitFund\ChitFund_Users;
use App\Models\ChitFund\ChitFund_Dues;

use DB;
use URL;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Redirect;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ChitFundImportUsers implements ToModel, WithHeadingRow

{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $createdOn =  Carbon::now();
        $currentTime = Carbon::now(); 

        return new ChitFund_Users([
            'user_name'    => $row['user_name'],
            'mobile_no'    => $row['mobile_no'],
            'address'      => $row['address'],
            'plan_id'      => $row['plan_id'],
            'createdOn'    => $createdOn,
        ]);
    }
}
