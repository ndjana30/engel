<?php

namespace App\Http\Controllers;

use App\Http\Requests\Matieres\UpdateMatiereRequest;
use App\Models\Concours;
use Illuminate\Http\Request;
use App\Http\Requests\Matieres\StoreMatiereRequest;
use App\Models\Matiere;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class MatiereController extends ApiResponseControlller
{
    public function __construct()
    {
        $this->middleware('auth:api');


    }


    public function index(Request $request)
    {
        try {
            $request->validate(["concour_id" => 'integer|exists:concours,id']);
            if ($request->has('concour_id')) {
                $concour = Concours::find($request->concour_id);
                $matieres = $concour->matieres()->paginate(perPage: $request->per_page ?? 25, page: $request->page ?? 1);
            } else {
                $matieres = Matiere::paginate(perPage: $request->per_page ?? 25, page: $request->page ?? 1);
            }
            $result = $matieres->getCollection()->transform(function ($matiere) {
                $matiere->image = $matiere->image ? Storage::disk('public')->url($matiere->image) : null;
                return $matiere;
            });
            return $this->returnSucces($matieres->setCollection($result));
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
                $image = $this->storeFile(filePath: "Images/Matieres/" . $request->libelle, file: $file);
                $data['image'] = $image;
            }
            $matire = Matiere::create($data);
            return $this->returnSucces($matire);
        } catch (\Throwable $th) {
            return $this->returnError($th);
        }
    }
    public function update(UpdateMatiereRequest $request)
    {

       
        $this->middleware('admin');
        try {            
            $data = $request->all();
            $matiere = Matiere::find($request->matiere_id);
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $image = $this->storeFile(filePath: "Images/Matieres/" . $request->libelle, file: $file);
                $data['image'] = $image;
            }
            if ($matiere->image != null) {
                Storage::disk('public')->delete($matiere->image);
            }
            
            // dd($matiere)                        ;
            $matiere->update($data);
            return $this->returnSucces(Matiere::find($request->matiere_id));
        } catch (\Throwable $th) {
            // dd($th);
            return $this->returnError($th,code:$th->getCode());
        }
    }
    public function delete(Request $request)
    {
        // $this->middleware('admin');

        try {
            $request->validate(['matiere_id' => 'integer|exists:matieres,id|required']);
            $matiere = Matiere::find($request->matiere_id);
            if ($matiere->image != null) {
                Storage::disk('public')->delete($matiere->image);
            }
            $matireUpdate = $matiere->delete();
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
