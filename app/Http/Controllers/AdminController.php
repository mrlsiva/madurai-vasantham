<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

use App\Models\Admin;
use Validator;

class AdminController extends Controller
{
    /* Admin areas that share the admin login: heading on the login page and page to open after login */
    protected $modules = [
        'attendance' => ['heading' => 'Attendance Admin',               'home' => 'attendance.index'],
        'chitfund'   => ['heading' => 'Vasantham Siru Semippu Thittam', 'home' => 'chitfundIndex'],
        'spin'       => ['heading' => 'Spin Wheel Admin',               'home' => 'spin.admin'],
    ];

    //todo: admin login form
    public function login_form(string $module)
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route($this->modules[$module]['home']);
        }
        return view('admin.login-form', [
            'heading' => $this->modules[$module]['heading'],
            'action'  => route($module.'.login.validate'),
        ]);
    }

    //todo: admin login functionality
    public function login_validate(Request $request, string $module){
        $request->validate([
            'username'=>'required',
            'password'=>'required',
        ]);       

        if (Auth::guard('admin')->attempt(['username' => $request->username, 'password' => $request->password])) {
            return redirect()->route($this->modules[$module]['home']);
        }else{
            Session::flash('error-message','Invalid Username or Password');
            return back();
        }
    }

    //todo: admin logout functionality
    public function logout(string $module){
        Auth::guard('admin')->logout();
        return redirect()->route($module.'.login');
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
