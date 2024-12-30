<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Buyer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',  
        'shipping_address', 
    ];

    /**
     * Relation avec l'utilisateur.
     * Un Buyer appartient à un User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);  
    }
}
