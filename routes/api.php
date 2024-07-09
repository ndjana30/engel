<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();   
// });
Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/login', [App\Http\Controllers\UserController::class, 'login']);
        Route::post('/register', [App\Http\Controllers\UserController::class, 'register']);
        Route::post('/refresh-token', [App\Http\Controllers\UserController::class, 'refreshToken']);
        Route::get('/me', [App\Http\Controllers\UserController::class, 'me']);
    
        Route::put('/me/update', [App\Http\Controllers\UserController::class, 'update']);
    });
    
    Route::prefix('matieres')->group(
        function ()  {
            Route::post('/', [App\Http\Controllers\MatiereController::class, 'store'])->middleware('admin');
            Route::put('/', [App\Http\Controllers\MatiereController::class, 'update'])->middleware('admin');
            // Get matiers les diferrents paramettres sont dans la requette, 
            Route::get('/', [App\Http\Controllers\MatiereController::class, 'index']);
            //  delete matiere
            Route::delete('/', [App\Http\Controllers\MatiereController::class, 'delete'])->middleware('admin');;
        }
    );
    Route::prefix('concours')->group(
        function ()  {
            Route::post('/', [App\Http\Controllers\ConcoursController::class, 'store'])->middleware('admin');
            Route::put('/', [App\Http\Controllers\ConcoursController::class, 'update'])->middleware('admin');
            // Get matiers les diferrents paramettres sont dans la requette, 
            Route::get('/', [App\Http\Controllers\ConcoursController::class, 'index']);
            Route::delete('/', [App\Http\Controllers\ConcoursController::class, 'delete'])->middleware('admin');;
            Route::put('/add-matiere', [App\Http\Controllers\ConcoursController::class, 'addMatiereToConcours'])->middleware('admin');
            Route::put('/remove-matiere', [App\Http\Controllers\ConcoursController::class, 'removeMatiereToConcours'])->middleware('admin');

        }
    );
    Route::prefix('cours')->group(
        function ()  {
            Route::post('/', [App\Http\Controllers\CoursController::class, 'storeCours'])->middleware('admin');
            Route::put('/', [App\Http\Controllers\CoursController::class, 'updateCours'])->middleware('admin');
            // Get matiers les diferrents paramettres sont dans la requette, 
            Route::get('/', [App\Http\Controllers\CoursController::class, 'index']);
            // Route::delete('/', [App\Http\Controllers\CoursController::class, 'delete']);
        }
    );
    Route::prefix('abonnements')->group(
        function ()  {
            Route::post('/', [App\Http\Controllers\AbonnementsController::class, 'store']);
            Route::put('/', [App\Http\Controllers\AbonnementsController::class, 'activerCode'])->middleware('admin');
            // Get matiers les diferrents paramettres sont dans la requette, 
            Route::get('/', [App\Http\Controllers\AbonnementsController::class, 'index']);
            // Route::delete('/', [App\Http\Controllers\CoursController::class, 'delete']);
        }
    );

    Route::prefix('code')->group(function ()  {
        Route::put('/',[App\Http\Controllers\CodesController::class, 'activate_code'] );
        Route::get('/',[App\Http\Controllers\CodesController::class, 'index'] );
        
    });
    
});