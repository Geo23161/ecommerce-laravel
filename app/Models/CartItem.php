<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = [
        'product_id',
        'quantity', 
        'ordered_at',
        'order_id'
    ];

    protected $with = ['product'];

    // Relation avec la commande
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    /**
     * Relation avec le produit.
     */
    public function product()
    {
        return $this->belongsTo(Produit::class, "product_id");
    }
}
