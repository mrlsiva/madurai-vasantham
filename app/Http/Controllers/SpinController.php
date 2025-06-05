<?php

namespace App\Http\Controllers;

use App\Models\Spin;
use App\Models\Spin_Invoice;
use Illuminate\Http\Request;
use Validator;
use Carbon\Carbon;
use Illuminate\Database\QueryException;

use App\Imports\SpinImportInvoice;
use Maatwebsite\Excel\Facades\Excel;

use Config;
use DB;
use Illuminate\Support\Facades\File;
use Session;
use URL;

class SpinController extends Controller
{
    public $successStatus = 200;

    /* Show Invoice Entry Form */
    public function index()
    {
        //$spins = Spin::orderByDesc('id')->paginate(20);
        $spins = Spin::orderByDesc('id')->get();
        return view('admin.spins', compact('spins'));
    } 
   
    /* Show Invoice Entry Form */
    public function showForm()
    {
        return view('spin-form');
    }   

    public function getSpinDirectory($userId)
    {        
        $year  = Carbon::now()->format('Y');
        $month = Carbon::now()->format('m');
        $day   = Carbon::now()->format('d');

        return array(
            'storagePath' => public_path('uploads/spin/'.$year.'/'.$month.'/'.$day.'/'.$userId),
            'storageDir'  => $year.'/'.$month.'/'.$day.'/'.$userId
        );        
    }

    public function createSpinDirectory($userId)
    {
        $path = $this->getSpinDirectory($userId );

        if(!File::isDirectory($path['storagePath'])){
            File::makeDirectory($path['storagePath'], 0777, true, true);
        }   
    } 

    /**
     * Get user spin uploded image
     *
     * @return array boolean & image src
     */
    public static function getUserSpinMedia($imageUrl)
    { 
        $is_exists = false;

        if( $imageUrl != '' && file_exists(public_path('/uploads/spin/'.$imageUrl)) ) {
            $img_src = URL::to('/public/uploads/spin/'.$imageUrl ); 
            $is_exists = true;
        }    
        else {
            $img_src = URL::to('/public/uploads/thumb/user-thumb.png'); 
       }  
       return array( 'is_exists' => $is_exists, 'img_src' => $img_src);
    }

    /* Save Invoice Entry Form Data */
    public function saveInvoiceForm(Request $request)
    {
        try {        
            $validator = Validator::make($request->all(), [ 
                'username' => 'required|min:3', 
                //'email' => 'required', 
                //'mobile' => 'required|unique:spins|min:10',
                'mobile' => 'required|min:10',  
                'branch' => 'required',  
                //'invoice_copy' => 'required|mimes:pdf,jpg,jpeg,png|max:8192', //4MB
                'invoice_number' => 'required|min:3', 
                'discount' => 'nullable', 
                'expires_at' => 'nullable',
                'is_redeemed'  => 'nullable'          
            ]);

            if ($validator->fails()) { 
                return redirect()->back()->withErrors($validator->errors())->withInput($request->input());               
            }

            $input = $request->all(); 
            $branch = strtolower($input['branch']);

           $hasSpin = Spin::where('invoice_number', $input['invoice_number'])                
                ->where('branch', $input['branch'])
                ->get();
            if( $hasSpin->count() > 0 ){
                $error['message'] = 'Your Invoice Number already registered!!';                
                return redirect()->back()->withErrors( $error )->withInput($request->input());  
            }

            $hasSpinInvoice = Spin_Invoice::where($branch, $input['invoice_number'])          
            ->get();   
            if( $hasSpinInvoice->count() <= 0 ){
                $error['message'] = 'Invalid Invoice!!';
                return redirect()->back()->withErrors( $error )->withInput($request->input());  
            }

            $spinFormData = new Spin();
            $spinFormData->name = $input['username'];            
            //$spinFormData->email = $input['email'];
            $spinFormData->mobile = $input['mobile'];
            $spinFormData->branch = $input['branch'];

            $invoice_number  = $input['invoice_number'];
            $spinFormData->invoice_number  = $invoice_number;

            /* $this->createSpinDirectory($invoice_number);
            $dir = $this->getSpinDirectory($invoice_number);
            $fileName = $invoice_number.'_'.time().'.'.$request->invoice_copy->extension(); 
            $request->invoice_copy->move($dir['storagePath'], $fileName);                        
            $spinFormData->invoice_copy  = $dir['storageDir'].'/'.$fileName; */
                    
            $spinFormData->save();        
        
            return redirect()->route('showSpinWheel', ['invoice_number' => $spinFormData->id]);

           // return redirect()->back()->with('success', 'Your Invoice submitted successfully!');
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

    /* Update Invoice Data */
    public function updateInvoiceForm(Request $request, $id)
    {
        try {    
            if( !empty($id) ){
                $updatedata['is_redeemed'] = 1; 
                $upadte = Spin::where('id', $id)->update($updatedata);         
                    
                return redirect()->back()->with('success', 'Invoice updated successfully!');
            } else{
                return redirect()->back()->with('error', 'Error!. Invoice not updated');
            }
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

    

     /* Show Spin Wheel */
     public function showSpinWheel(Request $request, $invoice_number)
     {
        $allowed = false;

        if( !empty($invoice_number) ){
            $hasSpin = Spin::where('id', $invoice_number)
                ->where('discount', NULL)
                ->get();
            if( $hasSpin->count() > 0 ){
                $allowed = true;
                
            }else{                
                $allowed = false;
            }
        }else{                
            $allowed = false;
        } 
        
        if($allowed){
            return view('spin-win');
        }else{
            return redirect()->route('showForm');
        }
    }

    /* Save Invoice Entry Form Data */
    public function saveSpinWheel(Request $request, $invoice_number, $discount )
    {
        if( !empty( $invoice_number ) && !empty( $discount ) ) {
            //Spin::where('invoice_number', $invoice_number)->update(['discount'=> $discount]);
            Spin::where('id', $invoice_number)->update(['discount'=> $discount]);
            return redirect()->route('thankYou', ['invoice_number' => $invoice_number]);
        }       
    }

    /* Thank You */
    // public function thankYou()
    // {
    //     return view('spin-thankyou');
    // }  
    // public function thankYou($invoice_number, $discount)
    // {
    //     return view('thankYou', compact('invoice_number', 'discount'));
    // }
    public function getPrizeByDiscount($discount)
    {
        // Determine the prize based on the discount value
        switch ($discount) {
            case 1:
                return "Glass Set";
            case 2:
                return "Appa Chatty";
            case 3:
                return "Crackers Gift Box";
            case 4:
                return "Plastic Container Set";
            case 5:
                return "Hot Box";
            case 6:
                return "2Ltr Water Bottle";
            default:
                return "No prize";
        }
    }
   
    public function thankYou($invoice_number)
    {
        $spindata = Spin::find($invoice_number);
        if (!$spindata) {
            abort(404); // Record not found
        }        
        // Get the prize based on the discount
        $prize = $this->getPrizeByDiscount($spindata['discount']);

        return view('spin-thankyou', compact('prize', 'spindata'));
    }

    public function ImportInvoice(){
        return view('admin.spins-import-invoice');
    }    
    
    public function spinImportInvoiceExcel(Request $request)
    { 
        // Validate the uploaded file
        $request->validate([
            'invoice' => 'required|mimes:xlsx,xls,csv',
        ]);               

        try {   
            // Get the uploaded file
            $file = $request->file('invoice');

           /* $file_name = $file->getClientOriginalName();
            $file_Extension = $file->getClientOriginalExtension();

            $destinationPath = public_path().'\uploads\spin';
            $file->move($destinationPath,$file->getClientOriginalName());

            $import_file = $destinationPath .'/'. $file_name; */           

            // Process the Excel file
            $result = Excel::import(new SpinImportInvoice, $file);

            if($result){
                return redirect()->back()->with('success','Invoice file imported successfully');
            }else{
                return redirect()->back()->with('error','Something Went Wrong');
            }
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
    
    /* Show All Spin Invoices */
    public function spinInvoices()
    {        
        $invoices = Spin_Invoice::orderByDesc('id')->get();
        return view('admin.spins-invoices', compact('invoices'));
    } 
}
