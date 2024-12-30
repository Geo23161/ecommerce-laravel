<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produit extends Model
{
    protected $fillable = [
        'title',
        'price',
        'description',
        'image',
        'rating',
        'discount',
        'seller_id',
        'quantite'
    ];

    /**
     * Relation : Un produit appartient à un vendeur.
     */
    public function seller()
    {
        return $this->belongsTo(Seller::class, 'seller_id');
    }
}
