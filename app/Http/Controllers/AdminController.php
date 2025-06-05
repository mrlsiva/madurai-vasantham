<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

use App\Models\Admin;
use Validator;

class AdminController extends Controller
{
    //todo: admin login form
    public function login_form()
    {
        return view('admin.login-form');
    }

    //todo: admin login functionality
    public function login_validate(Request $request){
        $request->validate([
            'username'=>'required',
            'password'=>'required',
        ]);       

        if (Auth::guard('admin')->attempt(['username' => $request->username, 'password' => $request->password])) {
            return redirect()->route('chitfundIndex');
        }else{
            Session::flash('error-message','Invalid Username or Password');
            return back();
        }
    }

    //todo: admin logout functionality
    public function logout(){
        Auth::guard('admin')->logout();
        return redirect()->route('login.form');
    }

    
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
