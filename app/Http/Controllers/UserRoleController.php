<?php

namespace App\Http\Controllers;

use App\Models\Roles;
use Illuminate\Support\Facades\Auth; 
use Validator;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Database\QueryException;

class UserRoleController extends Controller
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
        
    }

    /**
     * Display all.
     */
    public function showallRoles()
    {
        try {             
            $roles = Roles::select(['roleId', 'name', 'description', 'createdOn'])                  
                    ->orderBy('roleId', 'asc') 
                        ->paginate(20);
            
            if( $roles->total() > 0 ){
                return response()->json(['success'=> true, 'message' => 'Has roles records', 'data' => $roles ], $this->successStatus);
            }else{
                return response()->json(['success'=> false, 'message' => 'No roles records found', 'data' => NULL ], $this->successStatus);    
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
     * Get User role by ID.
     */
    public function getUserRole(string $id)
    {
        try {             
            $userRole = Roles::select(['roleId', 'name', 'description', 'createdOn'])                  
            ->where('roleId', $id)->first();
            
            if( $userRole ){
                return response()->json(['success'=> true, 'data' => $userRole], $this->successStatus);
            }else{
                return response()->json(['success'=> false, 'message' => 'No roles records found', 'data' => NULL ], $this->successStatus);    
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
    public function storeRole(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [ 
               'name' => 'required',              
           ]);
           if ($validator->fails()) { 
               return response()->json(['error'=>$validator->errors()], 401);            
           }
           $input = $request->all();  
          
           $input['createdBy'] =  '0'; 
           $input['createdOn'] =  Carbon::now();                     
           
           $role = Roles::create($input);           

           return response()->json(['success'=> true, 'message' => 'User Role Created'], $this->successStatus); 
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
    public function updateRole(Request $request, $roleId)
    {
        try {
            if (!empty($roleId)){ 
                    $validator = Validator::make($request->all(), [               
                    'name' => 'required',              
                ]);
                if ($validator->fails()) { 
                    return response()->json(['error'=>$validator->errors()], 401);            
                }

                $input = $request->all(); 
                $input['name'] = $request->name;  
                $input['modifiedBy'] =  0;
                $input['modifiedOn'] =  Carbon::now();     
                
                $roles = Roles::where('roleId', $roleId)
                    ->update($input); 

                return response()->json(['success'=> true, 'message' => 'User Role Updated'], $this->successStatus); 
            } else {
                return response()->json(['success'=> false, 'message' => 'User Role Id required'], $this->successStatus);     
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
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
