<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cours extends Model
{
    protected $fillable = ['titre', 'resume', 'image', 'matiere_id','video'];
    use HasFactory;

    public function matiere()
    {
        return $this->belongsTo(Matiere::class);
    }

    
}
