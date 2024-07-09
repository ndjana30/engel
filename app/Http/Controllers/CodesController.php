<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CodesController extends ApiResponseControlller
{

    public function __construct()
    {
        $this->middleware('auth:api');
    }


    public function index(Request $request)
    {
        try {
                $codes = \App\Models\Code::with('user')->paginate(25, page: $request->page ?? 1);          
            return $this->returnSucces($codes);
        } catch (\Throwable $th) {
            return $this->returnError($th);
        }
    }

    public function activate_code(Request $request){
        try {
            
        $request->validate([
            'code'=> ['exists:codes,code','string']
        ]);
        $code = \App\Models\Code::where('code', $request->code)->first();
        if($code->status ==1){
            return $this->returnError(new \Exception("This code is already in use"));
        }
        $abonnement  = $code->abonnement;
        $code->status= true;
        $code->user_id = $request->user()->id;
        $abonnement->status = 'USING';
        $code->activation_date = now();
        $code->save();
        $abonnement->save();
        return $this->returnSucces(\App\Models\Code::with('user')->find($code->id));
        } catch (\Throwable $th) {
            return $this->returnError($th);
        }
    }
}
