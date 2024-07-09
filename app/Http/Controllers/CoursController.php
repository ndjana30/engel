<?php

namespace App\Http\Controllers;

use App\Http\Requests\Cours\StoreCoursRequest;
use App\Http\Requests\Cours\UpdateCoursRequest;
use App\Models\Abonnements;
use App\Models\Concours;
use App\Models\Matiere;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Cours;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;


class CoursController extends ApiResponseControlller
{

    public function __construct()
    {
        $this->middleware('auth:api');
    }
    //
    public function index(Request $request)
    {
        try {
            $isAdmin = $request->user()->rule_id==1;
            $request->validate(['matiere_id'=> 'nullable|exists:matieres,id|integer']);
            if(!$isAdmin){
                $request->validate(['matiere_id'=> 'required|exists:matieres,id|integer','concour_id'=> 'required|exists:concours,id|integer']);
            }
           

            if ($request->has('matiere_id')) {
                $matiere = Matiere::find( $request-> matiere_id);
                $concoursList = $matiere->cours()->paginate(perPage: $request->per_page ?? 25, page: $request->page ?? 1);               
                $result = $concoursList->getCollection()->transform( function ($cour) use ($matiere, $request, $isAdmin){
                   
                    if($request->has('concour_id') && !$isAdmin){
                        $abonnement = Abonnements::where('user_id',$request->user()->id)->where('concours_id',$request->concour_id) ?->first();
                        Log::info($abonnement);
                        if($abonnement != null && $abonnement->status == 'USING'){
                            $cour->public = true;                                         
                        }  else $cour->public = $cour->public==0? false:true;                     
                    }
                    $cour->image = $cour->image? Storage::disk('public')->url($cour->image):null;  
                    $cour->video = Storage::disk('public')->url('Videos/'.$matiere->libelle.'/'. $cour->video);
                    return $cour;
                });
            } else{
                $concoursList = Cours::with('matiere')->paginate(perPage: $request->per_page ?? 25, page: $request->page ?? 1);
                $result = $concoursList->getCollection()->transform( function ($cour){
                    $matiere = $cour->matiere;
                    $cour->image = $cour->image? Storage::disk('public')->url($cour->image):null;  
                    $cour->video = Storage::disk('public')->url('Videos/'.$matiere->libelle.'/'.$cour->video);
                    return $cour;
                });
            }
           
            $concoursList->setCollection($result);
            return $this->returnSucces($concoursList);            
        } catch (\Throwable $th) {
            return $this->returnError($th);
        }
    }


    public function storeCours(StoreCoursRequest $request)
    {
        try {

            $data = $request->validated();
            $matire = Matiere::find($request->matiere_id);
            log::info($matire);
            // dd($matire);
            if ($request->hasFile('image') && $request->image != null) {
                $file = $request->file('image');
                $image = $this->storeFile(filePath: "Images/Cours/" . $matire->libelle, file: $file);
                $data['image'] = $image;
            }

            $file = $request->file('video');
            $video = $this->storeFile(filePath: "Videos/" . $matire->libelle, file: $file);
            if ($video != null) {
                $data['video'] = $video;
            }
            // implement image registration
            $cours = Cours::create($data);
            return $this->returnSucces($cours);
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
            return $this->returnError($th);
        }
    }

    public function updateCours(UpdateCoursRequest $request)
    {
        try {

            $data = $request->validated();
            $cours = Cours::find($request->cours_id);
            $matire = $cours->matiere();
            if ($request->hasFile('image') && $request->image != null) {

                $file = $request->file('image');
                $image = $this->storeFile(filePath: "Images/Cours/" . $request->$matire->libelle, file: $file);
                $data['image'] = $image;
                if ($cours->image != null) {
                    Storage::disk('public')->delete($cours->image);
                }
            }
            if ($request->has('video')) {
                $file = $request->file('video');
                $video = $this->storeFile(filePath: "Videos/" . $request->$matire->libelle, file: $file);
                if ($video != null) {
                    $data['video'] = $video;
                    Storage::disk('public')->delete($cours->video);
                }
            }

            // implement image registration
            $cours = $cours->update($data);
            return $this->returnSucces($cours);
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
            return $this->returnError($th);
        }
    }


    
    public function storeFile(string $filePath, $file, string $storedisk = "public", ): string|null
    {

        try {
            $fileExtention = $file->extension();
            $timestam = Carbon::now()->getTimestamp();
            $filename = $filePath . '/' . $timestam . "." . $fileExtention;
            $storage = Storage::disk($storedisk)->put($filename, file_get_contents($file));
            if ($storage) {
                return $timestam . "." . $fileExtention;;
            }
            return null;
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
            return null;
        }
    }

   


}
