<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Code extends Model
{
    use HasFactory;

    protected $fillable = ['code','abonnements_id'];

    public function abonnement() : BelongsTo {
        return $this->belongsTo(Abonnements::class, 'abonnements_id');
    }
    public function user() : BelongsTo {
        return $this->belongsTo(User::class, 'user_id');
    }
}
