<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use DB;
use Illuminate\Support\Facades\Auth; 
use Validator;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Database\QueryException;
use File;
use App\Http\Controllers\ShiftTimeController;
use App\Http\Controllers\UserController;

use App\Exports\AttendanceExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Redirect;
use URL;

class AttendanceController extends Controller
{
    
    public $successStatus = 200;
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {      
       //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    public function isPunchInLate(string $userId){
        $diffInHours = "";  
        $isPast = false;   
        $success['lateBy'] = $diffInHours;  

        /* To find Time Difference */                
        $ShiftTime = (new ShiftTimeController)->getUserShiftTime($userId); 
        $ShiftTime = $ShiftTime->getData();        
        if( $ShiftTime->success == true ){   
            $options = [
                'join' => ': ',
                'parts' => 1,
                'syntax' => CarbonInterface::DIFF_ABSOLUTE,
            ];
            $currentTime = Carbon::now();
            $startTime = Carbon::parse($ShiftTime->data->startTime);              
             //$startTime = Carbon::parse('01:26:00');  
            $diffInHours = $startTime->diffForHumans($currentTime, $options); 
            $isPast = $startTime->isPast();  
            if($isPast){
                $success['lateBy'] = $diffInHours .' Late';;
            }        
            return $success;
        }  
    }

    public static function getPunchInLateTime(string $userId, string $startTime = null){
        $diffInHours = "";  
        $isPast = false;   
        $success['lateBy'] = $diffInHours;

        if($userId != NULL || $userId != ''){ 
            /* To find Time Difference */                
            $ShiftTime = (new ShiftTimeController)->getUserShiftTime($userId); 
            $ShiftTime = $ShiftTime->getData();        
            if( $ShiftTime->success == true ){   
                $options = [
                    'join' => ': ',
                    'parts' => 2,
                    'syntax' => CarbonInterface::DIFF_ABSOLUTE,
                ];
                $currentTime = Carbon::now(); 

                if($startTime != NULL || $startTime != ''){
                    $punchInTime = $startTime;
                } else {
                    $hasAttendance = Attendance::where('userId', $userId)
                    ->where('startDate', $currentTime->toDateString())
                    ->get();
                    if( $hasAttendance->count() > 0 ){	                    
                        $punchInTime = $hasAttendance[0]['startTime'];
                    } 
                }              

                $punchInTime = Carbon::parse($punchInTime);  
                $diffInHours = $punchInTime->diffForHumans($ShiftTime->data->startTime, $options); 
                $diffInHoursText = $punchInTime->diffForHumans($ShiftTime->data->startTime);                     
                if( str_contains($diffInHoursText, 'after') ){
                    $success['lateBy'] = $diffInHours .' Late';
                }                 
            }  
        }
        return $success;
    }

    public function islogin(string $userId)
    {
        try {
            if($userId != NULL || $userId != ''){
                $currentTime = Carbon::now(); 
                
                $leaveStatus = $this->leaveStatus($userId);
                $leaveStatus = $leaveStatus->getData();               
                // if leave applied
                if( $leaveStatus->success == true && (isset($leaveStatus->data->atStatus) && $leaveStatus->data->atStatus == 1) ) {  
                    $loginData['atStatus'] = 4;
                    $loginData['message'] = 'Leave applied already';
                    return response()->json(['success'=> true, 'data' => $loginData], $this->successStatus);
                }

                $hasAttendance = Attendance::where('userId', $userId)
                ->where('startDate', $currentTime->toDateString())
                ->get();
                
                $loginData['currentTime'] = $currentTime->format('Y-m-d H:i:s');
                $result = $this->isPunchInLate($userId);                

                if( $hasAttendance->count() > 0 ){	
                    
                    $loginData['punchInTime'] = $hasAttendance[0]['startTime'];
                    $loginData['punchOutTime'] = $hasAttendance[0]['endTime'];
                    
                    $loginData['lateBy'] = $result['lateBy'];

                    if( $loginData['punchInTime'] ){
                        $loginData['atStatus'] = 2; // Punched In
                    }
                    if( $loginData['punchOutTime'] ){
                        $loginData['atStatus'] = 3; // Punched Out
                    }                                                    

                    return response()->json(['success'=> true, 'data' => $loginData], $this->successStatus);
                }else{ 
                          
                    /* To find Time Difference */                
                    $ShiftTime = (new ShiftTimeController)->getUserShiftTime($userId); 
                    $ShiftTime = $ShiftTime->getData();        
                    if( $ShiftTime->success == true ){ 
                        $loginData['punchInBtnTime'] = $ShiftTime->data->punchInBtnTime; 
                        $options = [
                            'join' => ': ',
                            'parts' => 1,
                            'syntax' => CarbonInterface::DIFF_ABSOLUTE,
                        ];
                        $currentTime = Carbon::now();
                        $punchInBtnTime = Carbon::parse($ShiftTime->data->punchInBtnTime);                                               
                        $diffInHours = $punchInBtnTime->diffForHumans($currentTime, $options); 
                        $isPast = $punchInBtnTime->isPast();  
                        if($isPast){
                            $loginData['atStatus'] = 1; // Allow Punch Button to Enable
                        }  else{
                            $loginData['atStatus'] = 0; // Dont Allow Punch Button 
                        }                       
                          
                    } else{
                        $loginData['atStatus'] = 0; // Dont Allow Punch Button 
                    } 
                    return response()->json(['success'=> false, 'data' => $loginData], $this->successStatus);
                }
            }else{
                return response()->json(['success'=> false, 'message' => 'Invalid User Id'], $this->successStatus);     
            } 
        }
        catch (\Throwable $exception) {
            return response()->json(['error'=> json_encode($exception->getMessage(), true)], 400 );
        } catch (\Illuminate\Database\QueryException $exception) {
            return response()->json(['error'=> json_encode($exception->getMessage(), true)], 400 );
        } catch (\PDOException $exception) {
            return response()->json(['error'=> json_encode($exception->getMessage(), true)], 400 );
            } catch (\Exception $exception) {
            return response()->json(['error'=> json_encode($exception->getMessage(), true)], 400 );
        }      
    }

    public function old_keep_it_islogin(string $userId)
    {
        try {
            if($userId != NULL || $userId != ''){
                $currentTime = Carbon::now();
                $hasAttendance = Attendance::where('userId', $userId)
                ->where('startDate', $currentTime->toDateString())
                ->get();
                
                $loginData['currentTime'] = $currentTime->format('Y-m-d H:i:s');
                
                if( $hasAttendance->count() > 0 ){	
                    
                    $loginData['punchInTime'] = $hasAttendance[0]['startTime'];
                    $loginData['punchOutTime'] = $hasAttendance[0]['endTime'];

                    $result = $this->isPunchInLate($userId);
                    $loginData['lateBy'] = $result['lateBy'];

                    if( $loginData['punchInTime'] ){
                        $loginData['atStatus'] = 1; // Punch In
                    }
                    if( $loginData['punchOutTime'] ){
                        $loginData['atStatus'] = 2; // Punch Out
                    }                                                    

                    return response()->json(['success'=> true, 'data' => $loginData], $this->successStatus);
                }else{ 
                    $loginData['atStatus'] = 0; // No Yet Punch In
                    return response()->json(['success'=> false, 'data' => $loginData], $this->successStatus);
                }
            }else{
                return response()->json(['success'=> false, 'message' => 'Invalid User Id'], $this->successStatus);     
            } 
        }
        catch (\Throwable $exception) {
            return response()->json(['error'=> json_encode($exception->getMessage(), true)], 400 );
        } catch (\Illuminate\Database\QueryException $exception) {
            return response()->json(['error'=> json_encode($exception->getMessage(), true)], 400 );
        } catch (\PDOException $exception) {
            return response()->json(['error'=> json_encode($exception->getMessage(), true)], 400 );
            } catch (\Exception $exception) {
            return response()->json(['error'=> json_encode($exception->getMessage(), true)], 400 );
        }      
    }

    public function getSelfieDirectory($userId)
    {        
        $year  = Carbon::now()->format('Y');
        $month = Carbon::now()->format('m');
        $day   = Carbon::now()->format('d');

        return array(
            'storagePath' => public_path('uploads/staffs/'.$userId.'/'.$year.'/'.$month.'/'.$day),
            'storageDir'  => $userId.'/'.$year.'/'.$month.'/'.$day
        );
        
    }

    public function createDirectory($userId)
    {
        $path = $this->getSelfieDirectory($userId );

        if(!File::isDirectory($path['storagePath'])){
            File::makeDirectory($path['storagePath'], 0777, true, true);
        }   
    }   

    public function in(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [ 
               'userId' => 'required',
               'shiftId' => 'required', 
               'associatedId' => 'required', 
               'imageUrl' => 'required',
           ]);
           if ($validator->fails()) { 
               return response()->json(['error'=>$validator->errors()], 401);            
           }
           //dd($request->all()); 
           $input = $request->all(); 
           //DB::enableQueryLog();
          
            $userId = $input['userId'];
            $currentTime = Carbon::now();
                         
            $islogin = $this->islogin($userId);
            $islogin = $islogin->getData();               

            $result = $this->isPunchInLate($userId);
            $success['currentTime'] = $currentTime->format('Y-m-d H:i:s');
            $success['punchInTime'] = $currentTime->format('Y-m-d H:i:s');
            $success['lateBy'] = $result['lateBy'];  

            if( $islogin->success == true ){	    
                $success['atStatus'] = 1; // Already Punch In            
                $success['message'] = 'Already Punch In';
                return response()->json(['success'=> true, 'data' => $success], $this->successStatus);
            }else{  
                $this->createDirectory($userId);
                $dir = $this->getSelfieDirectory($userId);
                $fileName = $userId.'_'.time().'.'.$request->imageUrl->extension(); 
                $request->imageUrl->move($dir['storagePath'], $fileName);   
                $input['imageUrl'] = $dir['storageDir'].'/'.$fileName;

                $input['startTime'] =  $currentTime;
                $input['startDate'] =  $currentTime;
                $input['status']    =  'A'; 
                $input['createdBy'] =  '0'; 
                $input['createdOn'] =  $currentTime;                     
                
                $attendance = Attendance::create($input);                  
               
                $success['atStatus'] = 1; // Punch In
                $success['message'] = 'Punch In Success';                 

                return response()->json(['success'=> true, 'data' => $success], $this->successStatus);
            } 
        }
        catch (\Throwable $exception) {
           return response()->json(['error'=> json_encode($exception->getMessage(), true)], 400 );
        } catch (\Illuminate\Database\QueryException $exception) {
           return response()->json(['error'=> json_encode($exception->getMessage(), true)], 400 );
        } catch (\PDOException $exception) {
           return response()->json(['error'=> json_encode($exception->getMessage(), true)], 400 );
         } catch (\Exception $exception) {
           return response()->json(['error'=> json_encode($exception->getMessage(), true)], 400 );
        }   
    }

    public function out(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [ 
               'userId' => 'required',                        
           ]);
           if ($validator->fails()) { 
               return response()->json(['error'=>$validator->errors()], 401);            
           }
           $input = $request->all(); 
           
           $input['endTime'] =  Carbon::now();
           $input['endDate'] =  Carbon::now();          
           $input['modifiedBy'] =  $input['userId'];
           $input['modifiedOn'] =  Carbon::now(); 
          
            $currentTime = Carbon::now();
            $islogin = $this->islogin($input['userId']);
            $islogin = $islogin->getData();            

            if( $islogin->success == true ){  

                if( is_null ($islogin->data->punchOutTime) ){
                    $hasAttendance = Attendance::where('userId', $input['userId'])
                    ->where('startDate', $currentTime->toDateString())
                    ->update($input); 
                }               

                return response()->json(['success'=> true, 'message' => 'Out Success'], $this->successStatus);
            }else{
                return response()->json(['success'=> false, 'message' => 'You are not punch in today'], $this->successStatus);     
            }
        }
        catch (\Throwable $exception) {
           return response()->json(['error'=> json_encode($exception->getMessage(), true)], 400 );
        } catch (\Illuminate\Database\QueryException $exception) {
           return response()->json(['error'=> json_encode($exception->getMessage(), true)], 400 );
        } catch (\PDOException $exception) {
           return response()->json(['error'=> json_encode($exception->getMessage(), true)], 400 );
         } catch (\Exception $exception) {
           return response()->json(['error'=> json_encode($exception->getMessage(), true)], 400 );
        }   
    }

    /**
     * get a listing of the resource.
     */
    public function logs(string $id)
    {
        try {
            if (!empty($id)){  
                $attendanceLogs = Attendance::select(['attandanceId', 'userId', 'startTime', 'endTime', 'startDate', 'endDate'])                  
                        ->orderBy('attandanceId', 'desc')        
                        ->where('userId', '=', $id)  
                         ->paginate(10);
               
                if( $attendanceLogs->total() > 0 ){
                    return response()->json(['success'=> true, 'message' => 'Has Log Entries', 'logs' => $attendanceLogs ], $this->successStatus);
                }else{
                    return response()->json(['success'=> false, 'message' => 'No records found for this user', 'logs' => NULL ], $this->successStatus);    
                }               

            } else {
                return response()->json(['success'=> false, 'message' => 'User Id required'], $this->successStatus);     
            }    
        }
        catch (\Throwable $exception) {
           return response()->json(['error'=> json_encode($exception->getMessage(), true)], 400 );
        } catch (\Illuminate\Database\QueryException $exception) {
           return response()->json(['error'=> json_encode($exception->getMessage(), true)], 400 );
        } catch (\PDOException $exception) {
           return response()->json(['error'=> json_encode($exception->getMessage(), true)], 400 );
         } catch (\Exception $exception) {
           return response()->json(['error'=> json_encode($exception->getMessage(), true)], 400 );
        }  
    }

    
    /* Get all user logs */
    public function allLogs()
    {
        try {  
            $attendancelogs =  Attendance::join('users', 'users.userId', '=', 'attendance.userId') 
                    ->select(['users.userId', 'users.firstName', 'users.lastName', 'attendance.attandanceId', 'attendance.userId', 'attendance.startTime', 'attendance.endTime', 'attendance.startDate', 'attendance.endDate'])
                    ->orderBy('attendance.attandanceId', 'desc')
                    ->paginate(10);
            
            return view('admin.user-logs', compact('attendancelogs'));
        }
        catch (\Throwable $exception) {
           return response()->json(['error'=> json_encode($exception->getMessage(), true)], 400 );
        } catch (\Illuminate\Database\QueryException $exception) {
           return response()->json(['error'=> json_encode($exception->getMessage(), true)], 400 );
        } catch (\PDOException $exception) {
           return response()->json(['error'=> json_encode($exception->getMessage(), true)], 400 );
         } catch (\Exception $exception) {
           return response()->json(['error'=> json_encode($exception->getMessage(), true)], 400 );
        }  
    }

    /* Get user log by its ID */
    public function userlog(string $id)
    {
        try {
               /* $attendancelogs =  Attendance::join('users', 'users.userId', '=', 'attendance.userId') 
                ->select(['users.userId', 'users.firstName', 'users.lastName', 'attendance.attandanceId', 'attendance.userId', 'attendance.startTime', 'attendance.endTime', 'attendance.startDate', 'attendance.endDate'])
                ->where('attendance.userId', '=', $id)  
                ->selectRaw('SEC_TO_TIME((TIME_TO_SEC(CASE WHEN SUBTIME(IFNULL(attendance.endTime,0),IFNULL(attendance.startTime,0)) > "00:00:00" THEN SUBTIME(IFNULL(attendance.endTime,0),IFNULL(attendance.startTime,0)) ELSE "00:00:00" END))) as total_hours')
                ->orderBy('attendance.attandanceId', 'desc')
                ->paginate(10); */
                               
                $attendancelogs =  DB::table('attendance as a1')
                ->where('a1.userId', '=', $id)	
                ->select( 'u.*', 'a1.userId as auId', 'a1.attandanceId', 'a1.startTime', 'a1.endTime', 'a1.startDate', 'a1.endDate', 'a1.imageUrl', 'a1.is_permission', 'a1.permission_startTime', 'a1.permission_endTime', 'a1.permission_imageUrl', 'a1.is_leave', 'a1.reason', 's.shiftId as shid', 's.shiftName', 's.startTime as shiftstartTime', 's.endTime as shiftendTime', DB::raw("TIME(a1.endTime) as eTime") , DB::raw("TIME(a1.startTime) as sTime") , DB::raw("TIME_FORMAT(SEC_TO_TIME((TIME_TO_SEC(CASE WHEN SUBTIME(IFNULL(TIME(a1.endTime),0),IFNULL(TIME(a1.startTime),0)) > '00:00:00' THEN SUBTIME(IFNULL(TIME(a1.endTime),0),IFNULL(TIME(a1.startTime),0)) ELSE '00:00:00' END))), '%H:%i:%s' )as total_hours"), DB::raw("IFNULL(TIME_FORMAT(SEC_TO_TIME(TIMESTAMPDIFF(SECOND, a1.permission_startTime, a1.permission_endTime)), '%H:%i:%S'), '00:00:00') as permissionInHours") )
                ->leftJoin('users as u', 'u.userId', '=', 'a1.userId')		
                ->leftJoin('shifttime as s', 's.shiftId', '=', 'a1.shiftId') 									
                ->groupBy('a1.userId')	
                ->groupBy('a1.attandanceId')
                ->orderBy('a1.attandanceId', 'desc')
                ->paginate(20);             

                return view('admin.user-logs', compact('attendancelogs'));    
        }
        catch (\Throwable $exception) {
           return response()->json(['error'=> json_encode($exception->getMessage(), true)], 400 );
        } catch (\Illuminate\Database\QueryException $exception) {
           return response()->json(['error'=> json_encode($exception->getMessage(), true)], 400 );
        } catch (\PDOException $exception) {
           return response()->json(['error'=> json_encode($exception->getMessage(), true)], 400 );
         } catch (\Exception $exception) {
           return response()->json(['error'=> json_encode($exception->getMessage(), true)], 400 );
        }  
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function exportAttendenceLogs(Request $request) 
    {
        try { 
            $userId = $request->id;
            $date = $request->date;

            if($userId != NULL || $userId != ''){ 
                $fileName = $userId.'.xlsx';

                $user = (new UserController)->getUserById($userId); 
                $user = $user->getData();        
                if( $user->success == true ){  
                    $userName = (new UserController)->getFullNameAttribute($user->data->firstName, $user->data->lastName, 'slug');
                    $m = $request->date ? $request->date : 'all';
                    $fileName = $userId.'-'.$userName.'-'.$m.'.xlsx';
                }         
                $exportData = new AttendanceExport($userId, $date);
            
                if( !is_null($exportData) || $exportData != null ){
                    
                    try {
                        return Excel::download($exportData, $fileName);
                    }
                    catch (\Throwable $exception) {
                        throw new \Exception("No Records Found for the month $date");
                    }
                    
                }else{                    
                    throw new \Exception("No Records Found  for the month $date");
                }                
            }
        }
        catch (\Throwable $exception) {           
          return back()->withErrors(['error' => json_encode($exception->getMessage(), true)]);
        }  
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

     /**
     * Get user selfie uploded image
     *
     * @return array boolean & image src
     */
    public static function getUserMedia($imageUrl)
    { 
        $is_exists = false;

        if( $imageUrl != '' && file_exists(public_path('/uploads/staffs/'.$imageUrl)) ) {
            $img_src = URL::to('/public/uploads/staffs/'.$imageUrl ); 
            $is_exists = true;
        }    
        else {
            $img_src = URL::to('/public/uploads/thumb/user-thumb.png'); 
       }  
       return array( 'is_exists' => $is_exists, 'img_src' => $img_src);
    }
    
    /* ***************************************************************** */
    public function permissionStatus(Request $request, $userId)
    {
        try {            
            if (!empty($userId)){                  
                                
                $currentTime = Carbon::now(); 
                    
                $hasPermission = Attendance::where('userId', $userId)
                ->where('startDate', $currentTime->toDateString())
                ->where('is_permission', 1)
                ->get();               
       
                if( $hasPermission->count() > 0 ){	
                    $permissionData['permissionStartTime'] = '';
                    $permissionData['permissionEndTime'] = '';

                    if( $hasPermission[0]['permission_startTime'] ){
                        $permissionData['permissionStartTime'] = $hasPermission[0]['permission_startTime'];
                        $permissionData['atStatus'] = 'Started'; // Permission Started                        
                    }
                    if( $hasPermission[0]['permission_endTime'] ){
                        $permissionData['permissionEndTime'] = $hasPermission[0]['permission_endTime'];
                        $permissionData['atStatus'] = 'End'; // Permission End                        
                    }   
                } else{
                    $permissionData['atStatus'] = 'Not Started'; // Permission Not Started
                }

                $leaveStatus = $this->leaveStatus($userId);
                $leaveStatus = $leaveStatus->getData();
               /* if( $leaveStatus->success == true ) {  
                    $permissionData['leaveStatus'] = $leaveStatus->data->atStatus;
                } */
                // if leave applied
                if( $leaveStatus->success == true && (isset($leaveStatus->data->atStatus) && $leaveStatus->data->atStatus == 1) ) {  
                    $permissionData['message'] = 'Leave applied already';
                    unset($permissionData['atStatus']);
                }

                $islogin = $this->islogin($userId);
                $islogin = $islogin->getData(); 
                // if punched out		
			    if( $islogin->success == true && (isset($islogin->data->atStatus) && $islogin->data->atStatus == 3) ){ 
                    $permissionData['message'] = 'Punched out applied already';
                    unset($permissionData['atStatus']);
                }                

                return response()->json(['success'=> true, 'data' => $permissionData], $this->successStatus);                
            }    
        }
        catch (\Throwable $exception) {
           return response()->json(['error'=> json_encode($exception->getMessage(), true)], 400 );
        } catch (\Illuminate\Database\QueryException $exception) {
           return response()->json(['error'=> json_encode($exception->getMessage(), true)], 400 );
        } catch (\PDOException $exception) {
           return response()->json(['error'=> json_encode($exception->getMessage(), true)], 400 );
         } catch (\Exception $exception) {
           return response()->json(['error'=> json_encode($exception->getMessage(), true)], 400 );
        }   
    } 


    public function applyPermission(Request $request, $userId)
    {
        try {            
            if (!empty($userId)){                  
                                
                $currentTime = Carbon::now();
                            
                $islogin = $this->islogin($userId);
                $islogin = $islogin->getData(); 

                if( $islogin->success == true ){	
                    
                    $success['currentTime'] = $currentTime->format('Y-m-d H:i:s');

                    // Prevent permission request if leave applied already
                    $leaveStatus = $this->leaveStatus($userId);
                    $leaveStatus = $leaveStatus->getData(); 

                    if( $leaveStatus->success == true &&  (isset($leaveStatus->data->atStatus) && $leaveStatus->data->atStatus == 1)) {  
                        $success['message'] = 'Leave applied already';                    
                        return response()->json(['success'=> false, 'data' => $success], $this->successStatus);
                    }

                    $hasPermission = Attendance::where('userId', $userId)
                    ->where('startDate', $currentTime->toDateString())
                    ->where('is_permission', 1)
                    ->get();                    
                    
                    // Close Permission
                    if( $hasPermission->count() > 0 ){	
                        $updatedata['permission_endTime'] =  $currentTime;                   
                        
                        $updateUser = Attendance::where('userId', $userId)
                        ->where('startDate', $currentTime->toDateString())
                        ->update($updatedata);                    
                        
                        $success['permissionStartTime'] = $hasPermission[0]['permission_startTime'];
                        $success['permissionEndTime'] = $currentTime->format('Y-m-d H:i:s');
                        $success['message'] = 'Permission Closed Success';  
                    } else{
                        // Start Permission
                        $validator = Validator::make($request->all(), [  
                            'reason' => 'required|min:5',                 
                            'imageUrl' => 'required'
                        ]);
                        if ($validator->fails()) { 
                            return response()->json(['error'=>$validator->errors()], 401);            
                        }              
                        $input = $request->all();

                        $this->createDirectory($userId);
                        $dir = $this->getSelfieDirectory($userId);
                        $fileName = $userId.'_'.time().'.'.$request->imageUrl->extension(); 
                        $request->imageUrl->move($dir['storagePath'], $fileName);  
                        
                        $updatedata['permission_startTime'] =  $currentTime;    
                        $updatedata['permission_imageUrl'] = $dir['storageDir'].'/'.$fileName;
                        $updatedata['is_permission'] =  1; 
                        $updatedata['reason'] =  $input['reason'];                        

                        $updateUser = Attendance::where('userId', $userId)
                        ->where('startDate', $currentTime->toDateString())
                        ->update($updatedata); 
                       
                        $success['permissionStartTime'] = $currentTime->format('Y-m-d H:i:s');
                        $success['message'] = 'Permission Request Success';   
                    }
                    return response()->json(['success'=> true, 'data' => $success], $this->successStatus);
                }else{
                    return response()->json(['success'=> false, 'message' => 'You must Punch In'], $this->successStatus);     
                }
            }    
        }
        catch (\Throwable $exception) {
           return response()->json(['error'=> json_encode($exception->getMessage(), true)], 400 );
        } catch (\Illuminate\Database\QueryException $exception) {
           return response()->json(['error'=> json_encode($exception->getMessage(), true)], 400 );
        } catch (\PDOException $exception) {
           return response()->json(['error'=> json_encode($exception->getMessage(), true)], 400 );
         } catch (\Exception $exception) {
           return response()->json(['error'=> json_encode($exception->getMessage(), true)], 400 );
        }   
    } 

    public function leaveStatus(string $userId){
        try {
            if($userId != NULL || $userId != ''){
                $currentTime = Carbon::now();  

                $hasLeave = Attendance::where('userId', $userId)
                ->where('startDate', $currentTime->toDateString())
                ->where('is_leave', '=', 1) 
                ->get();  

                $leaveData['atStatus'] = 0;
                $leaveData['message'] = 'Leave not applied'; 

                if( $hasLeave->count() > 0 ){	
                    $leaveData['atStatus'] = 1;   
                    $leaveData['message'] = 'Leave applied already';                
                }
                return response()->json(['success'=> true, 'data' => $leaveData], $this->successStatus);
            } 
        }
        catch (\Throwable $exception) {
            return response()->json(['error'=> json_encode($exception->getMessage(), true)], 400 );
        } catch (\Illuminate\Database\QueryException $exception) {
            return response()->json(['error'=> json_encode($exception->getMessage(), true)], 400 );
        } catch (\PDOException $exception) {
            return response()->json(['error'=> json_encode($exception->getMessage(), true)], 400 );
            } catch (\Exception $exception) {
            return response()->json(['error'=> json_encode($exception->getMessage(), true)], 400 );
        }               
    }

    public function applyLeave(Request $request, $userId)
    {
        try {            
            if (!empty($userId)){ 
                
                $validator = Validator::make($request->all(), [ 
                    'reason' => 'required|min:4'
                ]);
                if ($validator->fails()) { 
                    return response()->json(['error'=>$validator->errors()], 401);            
                }              
                $input = $request->all();
                                
                $currentTime = Carbon::now();
                $success['currentTime'] = $currentTime->format('Y-m-d H:i:s');
                $success['atStatus'] = 2; 
                $success['message'] = 'Leave apply request success';                
                
                $leaveStatus = $this->leaveStatus($userId);
                $leaveStatus = $leaveStatus->getData(); 

                if( $leaveStatus->success == true &&  (isset($leaveStatus->data->atStatus) && $leaveStatus->data->atStatus == 1)) {  
                    $success['atStatus'] = 1;
                    $success['message'] = 'Leave applied already';                    
                    return response()->json(['success'=> false, 'data' => $success], $this->successStatus);
                }

                $islogin = $this->islogin($userId);
                $islogin = $islogin->getData(); 
                if( $islogin->success == true ){
                    /* $updatedata['is_leave'] =  1; 
                    $updatedata['reason'] =  $input['reason'];
                   $updateUser = Attendance::where('userId', $userId)
                    ->where('startDate', $currentTime->toDateString())
                    ->update($updatedata); */

                    $success['atStatus'] = 0;
                    $success['message'] = 'Punch in already. Leave not allowed'; 

                    return response()->json(['success'=> true, 'data' => $success], $this->successStatus);
                }

                $ShiftTime = (new ShiftTimeController)->getUserShiftTime($userId); 
                $ShiftTime = $ShiftTime->getData();
                if( $ShiftTime->success == true ){ 
                    $shiftId = $ShiftTime->data->shiftId;
                    $associatedId = $ShiftTime->data->associatedId;

                    $input['userId'] =  $userId; 
                    $input['shiftId'] =  $shiftId; 
                    $input['associatedId'] =  $associatedId;
                    $input['startTime'] =  $currentTime;
                    $input['startDate'] =  $currentTime;
                    $input['status']    =  'A'; 
                    $input['createdBy'] =  '0'; 
                    $input['createdOn'] =  $currentTime; 
                    $input['is_leave'] =  1;                                   
                    
                    $attendance = Attendance::create($input);                      
                              
                    return response()->json(['success'=> true, 'data' => $success], $this->successStatus);
                }   
            }    
        }
        catch (\Throwable $exception) {
           return response()->json(['error'=> json_encode($exception->getMessage(), true)], 400 );
        } catch (\Illuminate\Database\QueryException $exception) {
           return response()->json(['error'=> json_encode($exception->getMessage(), true)], 400 );
        } catch (\PDOException $exception) {
           return response()->json(['error'=> json_encode($exception->getMessage(), true)], 400 );
         } catch (\Exception $exception) {
           return response()->json(['error'=> json_encode($exception->getMessage(), true)], 400 );
        }   
    }


    /**
     * get user logs by month.
     */
    public function monthlyLog(Request $request, string $id, string $year, string $month)
    {
        try {
            if (!empty($id)){  
                $selectedMonth = $year.'-'.$month;
                $presentLogs = Attendance::where('userId', $id)
                ->where('is_leave', '=', 0) 
                ->whereYear('startDate', '=', Carbon::parse($selectedMonth)->year)
                ->whereMonth('startDate', '=', Carbon::parse($selectedMonth)->month)
                ->select( 'startDate', 'is_permission', 'is_leave', DB::raw("DATE_FORMAT(startDate, '%d') as presents"))               
                ->pluck('presents');

                $permissionLogs = Attendance::where('userId', $id)
                ->where('is_permission', '=', 1) 
				->where('is_leave', '=', 0)
                ->whereYear('startDate', '=', Carbon::parse($selectedMonth)->year)
                ->whereMonth('startDate', '=', Carbon::parse($selectedMonth)->month)                
                ->select( 'startDate', 'is_permission', DB::raw("DATE_FORMAT(startDate, '%d') as permissions") )
                ->pluck('permissions');

                $leaveLogs = Attendance::where('userId', $id)
                ->where('is_leave', '=', 1) 
                ->whereYear('startDate', '=', Carbon::parse($selectedMonth)->year)
                ->whereMonth('startDate', '=', Carbon::parse($selectedMonth)->month)                
                ->select( 'startDate', 'is_leave', DB::raw("DATE_FORMAT(startDate, '%d') as leaves") )
                ->pluck('leaves'); 
                
                $data = array( 
                    'userId' => $id,
                    'date' => $selectedMonth,
                    'presents' => $presentLogs, 
                    'permissions' => $permissionLogs,
                    'leaves' => $leaveLogs
                );

                return response()->json(['success'=> true, 'data' => $data ], $this->successStatus);                         

            } else {
                return response()->json(['success'=> false, 'message' => 'User Id required'], $this->successStatus);     
            }    
        }
        catch (\Throwable $exception) {
           return response()->json(['error'=> json_encode($exception->getMessage(), true)], 400 );
        } catch (\Illuminate\Database\QueryException $exception) {
           return response()->json(['error'=> json_encode($exception->getMessage(), true)], 400 );
        } catch (\PDOException $exception) {
           return response()->json(['error'=> json_encode($exception->getMessage(), true)], 400 );
         } catch (\Exception $exception) {
           return response()->json(['error'=> json_encode($exception->getMessage(), true)], 400 );
        }  
    }

    /**
     * get user logs by day - date.
     */
    public function dayLog(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'userId' => 'required',
                'date' => 'required', 
            ]);
            if ($validator->fails()) { 
                return response()->json(['error'=>$validator->errors()], 401);            
            }
            $input = $request->all();             
                
            $attendancelog = DB::table('attendance as a1')
            ->where('a1.userId', '=', $input['userId'])	
            ->where('a1.startDate', '=', Carbon::parse($input['date']))	
            ->select( 's.startTime as checkInTime', 's.endTime as checkOutTime', DB::raw("DATE_FORMAT(a1.startTime, '%H:%i:%s') as actualCheckInTime"), DB::raw("DATE_FORMAT(a1.endTime, '%H:%i:%s') as actualCheckOutTime"), DB::raw("IFNULL(TIME_FORMAT(SEC_TO_TIME(TIMESTAMPDIFF(SECOND, a1.permission_startTime, a1.permission_endTime)), '%H:%i:%S'), '00:00:00') as permissionInHours") )
            ->leftJoin('users as u', 'u.userId', '=', 'a1.userId')		
            ->leftJoin('shifttime as s', 's.shiftId', '=', 'a1.shiftId') 									
            ->groupBy('a1.userId')	
            ->groupBy('a1.attandanceId')
            ->first();             
            
            return response()->json(['success'=> true, 'data' => $attendancelog ], $this->successStatus);  
            
        }
        catch (\Throwable $exception) {
           return response()->json(['error'=> json_encode($exception->getMessage(), true)], 400 );
        } catch (\Illuminate\Database\QueryException $exception) {
           return response()->json(['error'=> json_encode($exception->getMessage(), true)], 400 );
        } catch (\PDOException $exception) {
           return response()->json(['error'=> json_encode($exception->getMessage(), true)], 400 );
         } catch (\Exception $exception) {
           return response()->json(['error'=> json_encode($exception->getMessage(), true)], 400 );
        }  
    }

    /**
     * get user leave & permission logs for current month.
     */
    public function leaveLog(Request $request, string $id)
    {
        try {
            if (!empty($id)){ 
                $currentTime = Carbon::now();         
                
                $year = Carbon::parse($currentTime)->year;
                $month = Carbon::parse($currentTime)->month;
                $selectedMonth = $year.'-'.$month;

                $leaveLogs = Attendance::where('userId', $id)
                ->where('is_leave', '=', 1) 
                ->whereYear('startDate', '=', Carbon::parse($currentTime)->year)
                ->whereMonth('startDate', '=', Carbon::parse($currentTime)->month)                
                ->select( 'startDate', 'is_leave', DB::raw("DATE_FORMAT(startDate, '%d') as leaves") )
                ->get(); 
                
                $data = array( 
                    'userId' => $id,
                    'date' => $selectedMonth,                    
                    'leaves' => $leaveLogs
                );

                return response()->json(['success'=> true, 'data' => $data ], $this->successStatus);                         

            } else {
                return response()->json(['success'=> false, 'message' => 'User Id required'], $this->successStatus);     
            }    
        }
        catch (\Throwable $exception) {
           return response()->json(['error'=> json_encode($exception->getMessage(), true)], 400 );
        } catch (\Illuminate\Database\QueryException $exception) {
           return response()->json(['error'=> json_encode($exception->getMessage(), true)], 400 );
        } catch (\PDOException $exception) {
           return response()->json(['error'=> json_encode($exception->getMessage(), true)], 400 );
         } catch (\Exception $exception) {
           return response()->json(['error'=> json_encode($exception->getMessage(), true)], 400 );
        }  
    }

}
