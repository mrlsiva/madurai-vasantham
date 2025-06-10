<?php

namespace App\Imports;

use App\Models\ChitFund\ChitFund_Scheme; 
use App\Models\ChitFund\ChitFund_Users;
use App\Models\ChitFund\ChitFund_Dues;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ChitFundBulkImportUsers implements OnEachRow, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public static $failedRows = [];

    public function onRow(Row $row)
    {
        $rowIndex = $row->getIndex();
        $row = $row->toArray();

        // Validate the row
        $validator = Validator::make($row, [
            'user_name' => 'required',
            'mobile_no' => 'required|string|regex:/^[0-9]{10}$/',
            'address'   => 'required',
        ]);

        if ($validator->fails()) {
            // Log failed row with row number and error messages
            self::$failedRows[] = [
                'row' => $rowIndex, // 1-based index
                'errors' => $validator->errors()->all(),
                'data' => $row
            ];
            return; // Skip this row
        }

        $createdOn =  Carbon::now();
        $lastTokenId = ChitFund_Users::where('plan_id', request('plan_id'))->orderByDesc('token_id')->value('token_id');
        $token = $lastTokenId + 1;

        // Example: Create or update ChitFund_Users
        $user = ChitFund_Users::create(
            [
                'token_id'    => $token++,
                'user_name'   => $row['user_name'],
                'mobile_no'  => $row['mobile_no'],
                'address'     => $row['address'],
                'plan_id'     => request('plan_id'),
                'createdOn'   => $createdOn
            ]
        );

        if($user){               
            $plan = ChitFund_Scheme::where('plan_id', $user->plan_id)->get();               

            $data['user_id']    = $user->id;  
            $data['plan_id']    = $user->plan_id;  
            $data['start_date'] = $plan[0]->start_date;  
            $data['end_date']   = $plan[0]->end_date;  

            $duesCreated = $this->createDueEntriesForUser($data); 
        }
    }

    public function createDueEntriesForUser($data){

        if( $data ) {
            $currentTime = Carbon::now();

            $total_months = $this->findTotalMonths($data['start_date'], $data['end_date']);

            $records = [];
            $input = [];
            for ($x = 0; $x <= (int)$total_months-1; $x++) {
                $input['user_id']    =  $data['user_id'];  
                $input['plan_id']    =  $data['plan_id']; 
                $input['due_status'] =  0;                  
                $input['due_date']   = Carbon::parse($data['start_date'])->addMonths($x);
                $input['createdOn']  =  $currentTime;
                $input['created_at']  =  $currentTime;
                $records[] = $input;                 
            }

            $dues = ChitFund_Dues::insert($records);
            if($dues){
                return array('success' => true);
            }             
        } 
        return array('success' => false);
    }

    public static function findTotalMonths($start_date, $end_date){ 
        $start_date = Carbon::parse($start_date);
        $end_date   = Carbon::parse($end_date);
        return (int)round($start_date->floatDiffInMonths($end_date));
    }
}
