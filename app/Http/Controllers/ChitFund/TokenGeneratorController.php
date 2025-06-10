<?php

namespace App\Http\Controllers\ChitFund;

use App\Http\Controllers\Controller;
use App\Models\ChitFund\ChitFund_Users;
use Illuminate\Http\Request;

class TokenGeneratorController extends Controller
{
    public function tokenGenerate(Request $request) 
    {
        $plans = ChitFund_Users::select('plan_id')->distinct()->pluck('plan_id');

        foreach ($plans as $plan_id) {
            $users = ChitFund_Users::where('plan_id', $plan_id)->orderBy('user_id')->get();
            $counter = 1;

            foreach ($users as $user) {

                ChitFund_Users::where('user_id',$user->user_id)->update(['token_id'=>$counter++]);
            }
        }

        return "Success";
    }
}
