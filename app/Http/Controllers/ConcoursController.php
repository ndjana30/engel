<?php

namespace App\Http\Controllers;

use App\Http\Requests\Matieres\StoreMatiereRequest;
use App\Http\Requests\Matieres\UpdateConcoursRequest;
use App\Models\Abonnements;
use App\Models\Concours;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ConcoursController extends ApiResponseControlller
{
    public function __construct()
    {
        $this->middleware('auth:api');


    }

    public function index(Request $request){
        try {
            $concoursList = Concours::with('matieres')-> paginate(perPage: $request->per_page??25, page: $request->page??1);
            $result = $concoursList->getCollection()->transform(function ($matiere) use($request) {                
                $matiere->image = $matiere->image? Storage::disk('public')->url($matiere->image):null;  
                $abonnement = Abonnements::where('concours_id',$matiere->id)->where('user_id',$request->user()->id);                
                $matiere->status = $abonnement?->first()?->status?? 'None';                          
                
                return $matiere;
            });
            // Log::info($concoursList->setCollection($result)[0]);  
            return $this->returnSucces($concoursList->setCollection($result));

        } catch (\Throwable $th) {
            return $this->returnError($th);
        }
    }

    public function store(StoreMatiereRequest $request)
    {

        try {
            $data = $request->validated();
            if ($request->hasFile('image')) {
                $file = $request->file('image');            
                $image = $this->storeFile(filePath:"Images/Matieres/".$request->libelle,file: $file);
                $data['image'] = $image;
            }
            $matire = Concours::create($data);
            return $this->returnSucces($matire);
        } catch (\Throwable $th) {
            return $this->returnError($th);
        }
    }
    public function addMatiereToConcours(Request $request){
        try {
            $request->validate([
                'concour_id'=> 'integer|exists:concours,id|required',
                'matiere_id'=> 'integer|exists:matieres,id|required'
            ]);
        
            $concour =  Concours::findOrFail($request->concour_id);
            $concour->matieres()->attach($request->matiere_id);
            return $this->returnSucces(true);
        } 
        catch (\Throwable $th) {
            Log::info($th->getMessage());
            require $this->returnError($th);
        }
    }
    
    public function removeMatiereToConcours(Request $request){
        try {
            $request->validate([
                'concour_id'=> 'integer|exists:cours,id|required',
                'matiere_id'=> 'integer|exists:matieres,id|required'
            ]);
            
            $concour =  Concours::find($request->concour_id);
            $concour->matieres()->detach($request->matiere_id);
            return $this->returnSucces(true);
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
            require $this->returnError($th);
        }
        
    }
    public function update(UpdateConcoursRequest $request)
    {

        try {
            $data = $request->validated();
            $cours = Concours::find($request->concour_id);            
            if ($request->hasFile('image')) {
                $file = $request->file('image');            
                $image = $this->storeFile(filePath:"Images/Matieres/".$request->libelle,file: $file);
                $data['image'] = $image;
            }
            if($cours->image !=null){
                Storage::disk('public')->delete($cours->image);
            }
             $cours-> update($data);
            return $this->returnSucces($cours = Concours::find($request->concour_id));
        } catch (\Throwable $th) {
            return $this->returnError($th);
        }
    }
    public function delete(Request $request)
    {        
        try {
            $request->validate(['concour_id'=> 'exists:concours,id|required']);
            $cours = Concours::find($request->concour_id);
            if($cours->image !=null){
                Storage::disk('public')->delete($cours->image);
            }
            $matireUpdate = $cours->delete();
            return $this->returnSucces($matireUpdate);
        } catch (\Throwable $th) {
            return $this->returnError($th);
        }
    }


    public function storeFile(string $filePath, $file, string $storedisk = "public", ): string|null
    {

        try {
            $fileExtention = $file->extension();
            $timestam = Carbon::now()->getTimestamp();
            $filename = $filePath . '/' . $timestam . "." . $fileExtention;
            $storage = Storage::disk($storedisk)->put($filename, $file);
            if ($storage) {
                return $filename;
            }
            return null;
        } catch (\Throwable $th) { 
            Log::info($th->getMessage());
            return null;
        }
    }

}
