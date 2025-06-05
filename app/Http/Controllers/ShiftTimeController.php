<?php

namespace App\Http\Controllers;

use App\Models\ShiftTime;
use App\Models\ShiftTimeWithUsers;

use Illuminate\Support\Facades\Auth; 
use Validator;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Config;

class ShiftTimeController extends Controller
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
     * Display all.
     */
    public function showallShifts()
    {
        try {             
            $shiftTimes = ShiftTime::select(['shiftId', 'shiftName', 'startTime', 'endTime', 'status', 'effectiveFrom', 'effectiveTo', 'createdOn'])                  
                    ->orderBy('shiftId', 'asc') 
                        ->paginate(20);
            
            if( $shiftTimes->total() > 0 ){
                return response()->json(['success'=> true, 'message' => 'Has ShiftTime records', 'data' => $shiftTimes ], $this->successStatus);
            }else{
                return response()->json(['success'=> false, 'message' => 'No ShiftTimes records found', 'data' => NULL ], $this->successStatus);    
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
     * Store a newly created resource in storage.
     */
    public function storeShift(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [ 
               'shiftName' => 'required',
               'startTime' => 'required',
               'endTime' => 'required',              
           ]);
           if ($validator->fails()) { 
               return response()->json(['error'=>$validator->errors()], 401);            
           }
           
            $input = $request->all(); 
            $input['status'] =  'A'; 
            $input['effectiveFrom'] =  Carbon::now();
            $input['effectiveTo'] =  '9999-12-31';
            $input['createdBy'] =  '0'; 
            $input['createdOn'] =  Carbon::now();                    
           
            $shiftTime = ShiftTime::create($input);           

           return response()->json(['success'=> true, 'message' => 'Shift Timing Created'], $this->successStatus); 
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
    public function updateShift(Request $request, $shiftId)
    {
        try {
            if (!empty($shiftId)){ 
                $validator = Validator::make($request->all(), [ 
                    'shiftName' => 'required',
                    'startTime' => 'required',
                    'endTime' => 'required',              
                ]);
                if ($validator->fails()) { 
                    return response()->json(['error'=>$validator->errors()], 401);            
                }            

                $input = $request->all(); 
                $input['status'] =  'A'; 
                $input['effectiveFrom'] =  Carbon::now();
                $input['effectiveTo'] =  '9999-12-31';
                $input['createdBy'] =  '0'; 
                $input['createdOn'] =  Carbon::now();    
            
                 $shifttime = ShiftTime::where('shiftId', $shiftId)
                ->update($input); 

                return response()->json(['success'=> true, 'message' => 'Shift Timing Updated Succesfully'], $this->successStatus); 
            } else {
                return response()->json(['success'=> false, 'message' => 'Shift Id required'], $this->successStatus);     
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


    /* Setup shift time for user 
     * 
     * @return \Illuminate\Http\Response 
     */ 
    public function getUserShiftTime(string $userId) 
    { 
        try {
            $shifttimewithusers = ShiftTimeWithUsers::select(['userId', 'shiftId', 'associatedId'])
            ->where('userId', $userId)->first();                 
           
            if( !is_null($shifttimewithusers) ){
                $shiftId = $shifttimewithusers->shiftId; 
                $ShiftTime = ShiftTime::select(['shiftId', 'shiftName', 'startTime', 'endTime', 'effectiveFrom', 'effectiveTo'])
                ->where('shiftId', $shiftId)->first(); 
                if( !is_null($ShiftTime) ){

                    $punch_in_button_enable_time = Config::get('app.punch_in_button_enable_time'); 
                    $punch_out_button_enable_time = Config::get('app.punch_out_button_enable_time'); 
                    $currentTime = Carbon::now();
                    $carbonStartTime = Carbon::parse($ShiftTime->startTime);    
                    $ShiftTime['startTime'] = $ShiftTime->startTime;                
                    $carbonStartTime = $carbonStartTime->subMinutes($punch_in_button_enable_time)->format('H:i:s');

                    $carbonEndTime = Carbon::parse($ShiftTime->endTime);                    
                    $carbonEndTime = $carbonEndTime->subMinutes($punch_out_button_enable_time)->format('H:i:s');
                    $ShiftTime['currentTime'] = $currentTime->format('Y-m-d H:i:s');                    
                    $ShiftTime['punchInBtnTime'] = $carbonStartTime; 
                    $ShiftTime['punchOutBtnTime'] = $carbonEndTime; 
                    $ShiftTime['associatedId'] = $shifttimewithusers->associatedId;

                    return response()->json(['success'=> true, 'data' => $ShiftTime], $this->successStatus); 
                }else{
                    return response()->json(['success'=> false, 'data' => 'No shift time found'], $this->successStatus); 
                }                    
            }else{
                return response()->json(['success'=> false, 'data' => 'No shift time allocated for this user'], $this->successStatus); 
            }   
        }
        catch (\Throwable $exception) {
            return response()->json(['success'=> false, 'error'=> json_encode($exception->getMessage(), true)], 400 );
        } catch (\Illuminate\Database\QueryException $exception) {
            return response()->json(['success'=> false, 'error'=> json_encode($exception->getMessage(), true)], 400 );
        } catch (\PDOException $exception) {
            return response()->json(['success'=> false, 'error'=> json_encode($exception->getMessage(), true)], 400 );
        } catch (\Exception $exception) {
            return response()->json(['success'=> false, 'error'=> json_encode($exception->getMessage(), true)], 400 );
        }        
    }      

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
