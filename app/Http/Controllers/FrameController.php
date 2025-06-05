<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Carbon\Carbon;
use Illuminate\Database\QueryException;

use Illuminate\Support\Facades\File;
use Response;
use Session;
use URL;

class FrameController extends Controller
{
    public $successStatus = 200;

    /* Show Upload Form */
    public function frameIndex()
    {       
        return view('frame.frame-index');
    } 


    public function getFrameDirectory()
    {        
        $year  = Carbon::now()->format('Y');
        $month = Carbon::now()->format('m');
        $day   = Carbon::now()->format('d');

        return array(
            'storagePath' => public_path('uploads/frame/'.$year.'/'.$month.'/'.$day),
            'storageDir'  => $year.'/'.$month.'/'.$day
        );        
    }

    public function createFrameDirectory()
    {
        $path = $this->getFrameDirectory();

        if(!File::isDirectory($path['storagePath'])){
            File::makeDirectory($path['storagePath'], 0777, true, true);
        }   
    } 

    public function saveFrame( Request $request ){
        //just a random name for the image file
        $random = rand(1000, 100000);      

        //$_POST[data][1] has the base64 encrypted binary codes.
        //convert the binary to image using file_put_contents
      
        $currentTime = Carbon::now();
        $currentTime = $currentTime->format('Ydmhis');
        $this->createFrameDirectory();
        $dir = $this->getFrameDirectory();

        $image = base64_decode(explode(",", $request->image_url)[1]);
        $fileName = $currentTime.'.jpg'; 
        $image_dir  = $dir['storageDir'].'/'.$fileName;
        $image_path  = $dir['storagePath'].'/'.$fileName;
        $savedfile = @file_put_contents($image_path, $image);                     
        return response($image_path);
    }

    public function downloadframe( Request $request ){ 
        $validator = Validator::make($request->all(), [            
            'image_url' => 'required'            
        ]);

        if ($validator->fails()) { 
            return redirect()->back()->withErrors($validator->errors())->withInput($request->input());               
        }

        $input = $request->all();
        $imgURL = $input['image_url'];        
        return Response::download($imgURL);
    }
}
