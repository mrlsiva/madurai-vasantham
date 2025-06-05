<?php

namespace App\Exports;

use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use DB;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Redirect;

class AttendanceExport implements FromCollection, WithHeadings
{


    protected $id;

    function __construct($id, $date) {
        $this->id = $id;
        $this->date = $date;
    }

    /**
    * @return \Illuminate\Support\Collection
    * @query AttendanceController::userlog
    */
    public function collection()
    {
        $month = '';
        $currentTime = Carbon::now();
        if($this->date != NULL || $this->date != ''){ 
            $carbonCurrentDate = Carbon::parse($this->date);
            $month = $carbonCurrentDate->format('m');
            $opr = '=';
        }else{
            $month = $currentTime->format('m');
            $opr = '<=';
        }

        $attendancelogs =  DB::table('attendance as a1')
        ->where('a1.userId', '=', $this->id)	
        ->whereMonth('a1.startDate', $opr, $month)
        ->select( 
            'u.userId',
            'u.firstName', 
            'u.lastName',
            'u.email',
            'u.mobile', 
            's.shiftName', 
            'a1.startTime', 
            'a1.endTime', 
            'a1.startDate', 
            'a1.endDate',  
            DB::raw("TIME_FORMAT(SEC_TO_TIME((TIME_TO_SEC(CASE WHEN SUBTIME(IFNULL(TIME(a1.endTime),0),IFNULL(TIME(a1.startTime),0)) > '00:00:00' THEN SUBTIME(IFNULL(TIME(a1.endTime),0),IFNULL(TIME(a1.startTime),0)) ELSE '00:00:00' END))), '%H:%i:%s' )as total_hours"),
            DB::raw("IFNULL(TIME_FORMAT(SEC_TO_TIME(TIMESTAMPDIFF(SECOND, a1.permission_startTime, a1.permission_endTime)), '%H:%i:%S'), '00:00:00') as permissionInHours"), 
            DB::raw("CASE WHEN a1.is_leave = 1 THEN 'Leave' ELSE '' END as isleave")
            )
        ->leftJoin('users as u', 'u.userId', '=', 'a1.userId')		
        ->leftJoin('shifttime as s', 's.shiftId', '=', 'a1.shiftId') 									
        ->groupBy('a1.userId')	
        ->groupBy('a1.attandanceId')
        ->orderBy('a1.attandanceId', 'desc')
        ->get();

        if( $attendancelogs->count() > 0 ){	
            return $attendancelogs;
        } else{
          return NULL;            
        }                
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function headings(): array
    {
        return ["ID", "First Name", "Last Name", "Email", "Mobile", "Shift Name", "Punch In Time", "Punch Out Time", "Punch In Date", "Punch Out Date", "Total Hours", "Permission Hours", "Leave", "Late", "OverTime"];
    }
}
