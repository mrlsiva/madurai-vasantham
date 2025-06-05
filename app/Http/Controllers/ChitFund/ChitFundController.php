<?php

namespace App\Http\Controllers\ChitFund;
use App\Http\Controllers\Controller;

use App\Models\ChitFund\ChitFund_Scheme; 
use App\Models\ChitFund\ChitFund_Users;
use App\Models\ChitFund\ChitFund_Dues;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth; 
use Validator;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Config;
use DB;
use Illuminate\Support\Facades\File;
use Session;
use URL;
use Illuminate\Support\Facades\Http;

class ChitFundController extends Controller
{
    public $successStatus = 200;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function chitfundIndex()
    {       
        $schemes = ChitFund_Scheme::all();
        return view('admin.chitfund.index', compact('schemes'));
    }

    public function chitfundCreatePlan(Request $request)
    { 
       // return redirect()->back()->withErrors('Eror');
       // return redirect()->back()->with('success', 'Plan is created successfully'); 
        try {
            $validator = Validator::make($request->all(), [ 
                'plan_name' => 'required',
                'plan_amount' => 'required', 
                'start_date' => 'required', 
                'end_date' => 'required',
                'start_date_submit' => 'required',
                'end_date_submit' => 'required'               
            ]);
            if ($validator->fails()) { 
                return redirect()->back()->withErrors($validator->errors())->withInput($request->input());
            }
            $input = $request->all(); 
            $input['start_date'] = Carbon::parse( $input['start_date_submit'] );
            $input['end_date'] = Carbon::parse( $input['end_date_submit'] );
            $input['createdOn'] =  Carbon::now(); 

            unset($input['start_date_submit']);
            unset($input['end_date_submit']);           
            
            $scheme = ChitFund_Scheme::create($input); 
            
            return redirect()->back()->with('success', 'Plan '.$input['plan_name'].' created successfully'); 
        }
        catch (\Throwable $exception) {
            return redirect()->back()->withErrors( json_encode($exception->getMessage(), true) )->withInput($request->input());
        } catch (\Illuminate\Database\QueryException $exception) {
            return redirect()->back()->withErrors( json_encode($exception->getMessage(), true) )->withInput($request->input());
        } catch (\PDOException $exception) {
            return redirect()->back()->withErrors( json_encode($exception->getMessage(), true) )->withInput($request->input());
        } catch (\Exception $exception) {
            return redirect()->back()->withErrors( json_encode($exception->getMessage(), true) )->withInput($request->input());
        }    
    }

    public function chitfundShowPlan(Request $request, string $id)
    { 
        DB::enableQueryLog();
        $currentTime = Carbon::now();
        $users = DB::table('chitfund_scheme as s')
            ->where('s.plan_id', '=', $id)	
            ->select( 'u.user_id', 'u.user_name', 'u.mobile_no', 'u.address', 'u.plan_id as uplan_id', 's.plan_id', 's.plan_name', 's.start_date', 's.end_date')          	
            ->leftJoin('chitfund_users as u', 'u.plan_id', '=', 's.plan_id') 
            ->orderBy('u.createdOn', 'DESC') 
            ->orderBy('u.user_id', 'DESC')            
            ->get();   
           
        return view('admin.chitfund.users', compact('users'));    
    }


    public function chitfundCreateUser(Request $request)
    {     
        try {
            $validator = Validator::make($request->all(), [ 
                'user_name' => 'required',
                'mobile_no' => 'required', 
                'address' => 'required', 
                'plan_id' => 'required',                 
            ]);
            if ($validator->fails()) { 
                return redirect()->back()->withErrors($validator->errors())->withInput($request->input());
            }
            $input = $request->all();           
            $input['createdOn'] =  Carbon::now();
            $currentTime = Carbon::now(); 
            $whatsapp_url = '#';                    
            
            $user = ChitFund_Users::create($input); 
            if($user){               
                $plan = ChitFund_Scheme::where('plan_id', $user->plan_id)->get();               

                $data['user_id']    = $user->id;  
                $data['plan_id']    = $user->plan_id;  
                $data['start_date'] = $plan[0]->start_date;  
                $data['end_date']   = $plan[0]->end_date;  
               
                $duesCreated = $this->createDueEntriesForUser($data);  
                if( $duesCreated ) {
                    $date = $currentTime->format('d-m-Y');
                    $total_months = $this->findTotalMonths($data['start_date'], $data['end_date']);

                    $whatsapp_message = urlencode("Dear ".$input['user_name'].", Token Number ".$data['user_id']." You have successfully registered on Vasantham Siru Semippu Thittam ".$plan[0]->plan_name."(".$total_months." months) scheme with monthly due of Rs. ".$plan[0]->plan_amount.". Thank you. - Vasantham Home Appliances - Siru Semippu Thittam.");
                    $whatsapp_url = "https://wa.me/".$input['mobile_no']."?text=".$whatsapp_message;                    
                }                
            }
           return redirect()->back()->with(['success'=> 'User '.$input['user_name'].' added successfully', 'url' => $whatsapp_url ]); 
        }
        catch (\Throwable $exception) {
            return redirect()->back()->withErrors( json_encode($exception->getMessage(), true) )->withInput($request->input());
        } catch (\Illuminate\Database\QueryException $exception) {
            return redirect()->back()->withErrors( json_encode($exception->getMessage(), true) )->withInput($request->input());
        } catch (\PDOException $exception) {
            return redirect()->back()->withErrors( json_encode($exception->getMessage(), true) )->withInput($request->input());
        } catch (\Exception $exception) {
            return redirect()->back()->withErrors( json_encode($exception->getMessage(), true) )->withInput($request->input());
        }    
    }

    public function chitfundEditUser(Request $request)
    {     
        try {
            $validator = Validator::make($request->all(), [ 
                'user_id' => 'required',
                'user_name' => 'required',
                'mobile_no' => 'required', 
                'address' => 'required', 
                'plan_id' => 'required',                 
            ]);
            if ($validator->fails()) { 
                return redirect()->back()->withErrors($validator->errors())->withInput($request->input());
            }
            $input = $request->all();   
            
            $data['user_name']   = $input['user_name'];
            $data['mobile_no']   = $input['mobile_no'];
            $data['address']     = $input['address'];             
                       
            $user = ChitFund_Users::where('user_id', $input['user_id'])
                    ->where('plan_id', $input['plan_id'])
                    ->update($data);             
           
           return redirect()->back()->with(['success'=> 'User #'.$input['user_id'].'-'.$input['user_name'].' updated successfully.' ]); 
        }
        catch (\Throwable $exception) {
            return redirect()->back()->withErrors( json_encode($exception->getMessage(), true) )->withInput($request->input());
        } catch (\Illuminate\Database\QueryException $exception) {
            return redirect()->back()->withErrors( json_encode($exception->getMessage(), true) )->withInput($request->input());
        } catch (\PDOException $exception) {
            return redirect()->back()->withErrors( json_encode($exception->getMessage(), true) )->withInput($request->input());
        } catch (\Exception $exception) {
            return redirect()->back()->withErrors( json_encode($exception->getMessage(), true) )->withInput($request->input());
        }    
    }

    public static function findTotalMonths($start_date, $end_date){ 
        $start_date = Carbon::parse($start_date);
        $end_date   = Carbon::parse($end_date);
        return (int)round($start_date->floatDiffInMonths($end_date));
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

    public function isDuePaid(string $id)
    {
        try {
            if($id != NULL || $id != ''){
                $currentTime = Carbon::now();

                $dues = ChitFund_Dues::where('user_id', $id)
                ->whereMonth('createdOn', '=', $currentTime->month)
                ->whereYear('createdOn', '=', $currentTime->year)
                ->get();

                if( $dues->count() > 0 ){
                    return response()->json(['success'=> true], $this->successStatus);
                } else{
                    return response()->json(['success'=> false], $this->successStatus);
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

    public function chitfundAddDue(Request $request, string $planid, string $id)
    {
        try {
           
            if ( empty($planid) && empty($id) ){   
                return redirect()->back()->withErrors('Invalid UserId or PlanId');
            }
                       
            $currentTime = Carbon::now();                
            
            $isDuePaid = $this->isDuePaid($id);
            $isDuePaid = $isDuePaid->getData(); 

            if( $isDuePaid->success == true ){	           
                return redirect()->back()->with('success', 'Bill already generated for User #'.$id); 
            }
            
            $input['user_id'] =  $id;  
            $input['plan_id'] =  $planid;  
            $input['due_status'] = 0;
           // $input['due_date_paid'] =  $currentTime;
            $input['createdOn'] =  $currentTime;
            
            $dues = ChitFund_Dues::create($input); 
            
            return redirect()->back()->with('success', 'New bill genearted for User #'.$id); 
        }
        catch (\Throwable $exception) {
            return redirect()->back()->withErrors( json_encode($exception->getMessage(), true) )->withInput($request->input());
        } catch (\Illuminate\Database\QueryException $exception) {
            return redirect()->back()->withErrors( json_encode($exception->getMessage(), true) )->withInput($request->input());
        } catch (\PDOException $exception) {
            return redirect()->back()->withErrors( json_encode($exception->getMessage(), true) )->withInput($request->input());
        } catch (\Exception $exception) {
            return redirect()->back()->withErrors( json_encode($exception->getMessage(), true) )->withInput($request->input());
        }   
    }
    
    public function chitfundUserDetails(Request $request, string $id)
    { 
        $user = [];
        $chitUser = ChitFund_Users::where('user_id', $id)
        ->get();

        if( $chitUser->count() > 0 ){ 

            $dues = ChitFund_Dues::where('user_id', $id)
            ->where('plan_id', '=', $chitUser[0]->plan_id)
            ->get();

            if( $dues->count() < 1 ){               
                $plan = ChitFund_Scheme::where('plan_id', $chitUser[0]->plan_id)->get();               

                $data['user_id']    = $id;  
                $data['plan_id']    = $chitUser[0]->plan_id;  
                $data['start_date'] = $plan[0]->start_date;  
                $data['end_date']   = $plan[0]->end_date;  
               
                $duesCreated = $this->createDueEntriesForUser($data); 
            }  

            $user = DB::table('chitfund_dues as d')
                ->where('d.user_id', '=', $id)	
                ->select( 'u.plan_id as uplan_id', 'u.user_name', 'u.mobile_no', 'd.due_id', 'd.user_id', 'd.plan_id', 'd.due_status', 'd.due_date', 'd.due_date_paid', 's.plan_name', 's.plan_amount' )          	
                ->leftJoin('chitfund_users as u', 'u.user_id', '=', 'd.user_id')  
                ->leftJoin('chitfund_scheme as s', 's.plan_id', '=', 'd.plan_id')
                ->orderBy('d.due_id', 'ASC')	         
                ->get();     
        }        
        return view('admin.chitfund.user-details', compact('user'));    
    }  
   
    public static function getDueStatus($id, $key)
    { 
        $status = array( 0 => 'Un-Paid', 1 => 'Paid', 2 => 'Closed', 3 => 'Winner 1', 4 => 'Winner 2', 5 => 'Winner 3' );

        if( $key == 'array' ) { 
            $due_status = $status;
        }
        else if( ($id != '' || $id != NULL) && $key == 'string' ) {            
            $due_status = $status[$id];
        } else {
            $due_status = $status[0];
        }
        
        return $due_status;
    }

    public static function getUserDueStatus(string $id)
    {
        $result = array('success'=> false);

        if($id != NULL || $id != ''){
            $currentTime = Carbon::now();

            $dues = ChitFund_Dues::where('user_id', $id)
            ->whereMonth('due_date_paid', '=', $currentTime->month)
            ->whereYear('due_date_paid', '=', $currentTime->year)
            ->get();

            if( $dues->count() > 0 ){
                $result = array('success'=> true, 'data' => $dues);
            } 
        }  
        return $result;
    } 

    
    public function updateDueStatus(Request $request)
    {
        try {
           
            $validator = Validator::make($request->all(), [ 
                'due_id' => 'required',
                'due_status' => 'required'              
            ]);
            if ($validator->fails()) { 
                return redirect()->back()->withErrors($validator->errors())->withInput($request->input());
            }
            $input = $request->all(); 
                       
            $currentTime = Carbon::now(); 
            
            $updateBill['due_status'] =  $input['due_status'];
            if( $input['due_status'] == 1 ) {
                $updateBill['due_date_paid'] =  $currentTime;                    
            } 
            if( $input['due_status'] == 0 ) {
                $updateBill['due_date_paid'] =  null;                    
            }     
            $updateBill['modifiedOn'] =  $currentTime;             

           $updateResult = ChitFund_Dues::where('due_id', $input['due_id']) 
            ->update($updateBill); 
            
            return redirect()->back()->with('success', '#'.$input['due_id'].' bill status updated'); 
        }
        catch (\Throwable $exception) {
            return redirect()->back()->withErrors( json_encode($exception->getMessage(), true) )->withInput($request->input());
        } catch (\Illuminate\Database\QueryException $exception) {
            return redirect()->back()->withErrors( json_encode($exception->getMessage(), true) )->withInput($request->input());
        } catch (\PDOException $exception) {
            return redirect()->back()->withErrors( json_encode($exception->getMessage(), true) )->withInput($request->input());
        } catch (\Exception $exception) {
            return redirect()->back()->withErrors( json_encode($exception->getMessage(), true) )->withInput($request->input());
        }   
    }

    public static function getColorCode(string $status)
    {
        switch($status) {
            case('0'):
                $msg = 'warning';
                break;
 
            case('2'):  
                $msg = 'info';
                break;
 
            default:
                $msg = 'success';
        }
 
        return $msg;
    }

    
    public function printInvoice(Request $request, string $user_id, string $due_id ) 
    {
        try {
            if($user_id != NULL || $user_id != '' && $due_id != NULL || $due_id != ''){

                $user = DB::table('chitfund_dues as d')
                    ->where('d.due_id', '=', $due_id)
                    ->select( 'u.user_name', 'u.mobile_no', 'd.due_id', 'd.user_id', 'd.plan_id', 'd.due_status', 'd.due_date', 'd.due_date_paid', 's.plan_name', 's.plan_amount', DB::raw("SUM(d1.due_status) as total_dues_paid") )          	
                    ->leftJoin('chitfund_users as u', 'u.user_id', '=', 'd.user_id') 
                    ->leftJoin('chitfund_scheme as s', 's.plan_id', '=', 'd.plan_id')
                    ->leftJoin('chitfund_dues as d1', 'd1.user_id', '=', 'd.user_id') 
                    ->get();       
                  
                return view('admin.chitfund.print-invoice', compact('user'));  
                
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function getChitUsersData()
    {
        $api = url('/api/v1/chit/users/');
        $response = Http::withHeaders([
            'Authorization' => 'Bearer your_token_here',
            'Accept' => 'application/json',
            'trusted_ip' => '192.168.1.1',
            'bit-key' => '',
            'ssl' => ''
           // 'ssl_key' => ['/path/to/cert.pem', 'password.key']
        ])->get('https://api.example.com/data');
    
        if ($response->successful()) {
            // Handle the successful response
            return $response->json();
        } else {
            // Handle the error response
            return response()->json(['error' => 'Failed to fetch data'], $response->status());
        }
    }

}
