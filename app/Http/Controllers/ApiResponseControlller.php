<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;

class ApiResponseControlller extends Controller
{


    public function returnSucces($result=null, $message="Traitement effectué avec succès")
    {
        $response = [
            'success' => true,
            'data' => $result,
            'message' => $message,
        ];
        return response()->json($response, 200);
    }
    
    /*
    * return error response
    */

    public function returnError(\Throwable $exception, $message="Une erreur s'est produite", $code=400)
    {
        $error=null;
        if ($exception->getCode()==422) {            
                $error = $exception->getMessage();
                if ($exception instanceof \Illuminate\Validation\ValidationException) {
                    $errors = $exception->validator->errors();
                    $firstError = $errors->first();
                    $error .= ": " . $firstError;
                }
                
        }
        $response = [
            'success' => false,
            'data' => $error??$exception->getMessage(),
            'message' => $message,
        ];
        return response()->json($response, $code);
    }


}
