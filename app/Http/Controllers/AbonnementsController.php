<?php

namespace App\Http\Controllers;

use App\Events\SendMessageEvent;
use App\Jobs\SendSMSJobs;
use App\Models\Abonnements;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests\AbonnnementStoreRequest;
use Illuminate\Support\Facades\Log;
// use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class AbonnementsController extends ApiResponseControlller
{
    public function __construct() {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {try {
        
       $user = Auth::guard('api')->user();
       if($user->rule_id==1){
        $abonnements =  Abonnements::with('code', 'user')-> paginate(25, page: $request->page ?? 1);
       }else{
        $userModel = User::find($user->id);
        $abonnements = $userModel->abonnements()->with('concour')-> paginate();
       }
    return $this->returnSucces($abonnements);
    } catch (\Throwable $th) {
       return $this->returnError($th);
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
    public function store(AbonnnementStoreRequest $request)
    {
        try {
            $datas = $request->validated();
        $datas['user_id'] = $request->user()->id;
        $result = Abonnements::create($datas);
        return  $this->returnSucces(Abonnements::with('concour')->find($result->id));
        } catch (\Throwable $th) {
            return $this->returnError($th);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Abonnements $abonnements)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Abonnements $abonnements)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Abonnements $abonnements)
    {
        //
    }

    public function activerCode(Request $request)  {
        try {
            $request->validate(['abonnement_id'=>'integer|required|exists:abonnements,id']);
            $abonnement  = Abonnements::find($request->abonnement_id);  
            // dd($abonnement);
            if($abonnement->status=="VALIDATED"){
            return $this->returnError(new \Exception('This subscription is already activated'), message:"This subscription has already been activated");
            }
          $code =   $this->saveOneCode($abonnement->id);
            $abonnement->activation_date = Carbon::now();       
            $abonnement->status = "VALIDATED";
            SendSMSJobs::dispatchIf($code!=null,$abonnement, $code)->delay(now());
            // event(new SendMessageEvent($abonnement, $code));
            
            $abonnement->save();
            // $abonnement->      
            return $this->returnSucces($abonnement)    ;
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
           return $this->returnError($th);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Abonnements $abonnements)
    {
        //

    }

    protected function genererCodeActivation($id_paiement_attente): string
    {
        $formatDate = 'd/m/Y';
        $formatHeure = 'H:i:s';
        $dateActuelle = date($formatDate);
        $heureActuelle = date($formatHeure);

        $dateActuelleDetail = explode('/', $dateActuelle);
        $heureActuelleDetail = explode(':', $heureActuelle);
        $code = $dateActuelleDetail[1] . $dateActuelleDetail[0] . $dateActuelleDetail[2] . "" . $id_paiement_attente;
        $finalCode = "";
        for ($i = 0; $i < 10; $i++) {
            $index = rand(0, strlen($code) - 1);
            $finalCode .= $code[$index];
        }
        return "C" . $finalCode;
    }

    protected function saveOneCode(int $paiement_id): string|null
    {
        try {
            $id = $paiement_id;
            $code = $this->genererCodeActivation($id);
            // dd($code);
            while (\App\Models\Code ::where('code', $code)->exists()) {
                $code =     $this->genererCodeActivation($id);
            }
            \App\Models\Code::create([
                'abonnements_id' => $id,
                'code' => $code,
            ]);
            return $code;
        } catch (\Throwable $th) {        
            return null;
        }
    }

}

/**
 * SenderID= MonProf; X-Api-Key =AE9A5590-7F89-4095-B2FD-F33335A21510          
 * X-Secret = Qf5Ap7OQYCUeXxlbbs2ZqmSCiq7pBqSX0VyZAjM5rL36
 */