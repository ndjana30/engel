<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Abonnements extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'concours_id',
        'status',
        'activation_date',
        'user_number',
        'client_number',
    ];

    public function user() : BelongsTo {
        return $this->BelongsTo(User::class);
    }
    public function concour() : BelongsTo {
        return $this->belongsTo(Concours::class,'concours_id');
    }
    public function code() : HasOne {
        return $this->hasOne(Code::class);
    }

    

}
