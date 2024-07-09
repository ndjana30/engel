<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Matiere extends Model
{
    protected $fillable = ['libelle', 'description', 'image'];
    use HasFactory;

    public function cours(){
        return $this->hasMany(Cours::class);
    } 

    

}

