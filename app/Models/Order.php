<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'total',
        'date',  // la date à laquelle la commande a été faite
    ];

    protected $with = ['items'];

    // Relation avec l'utilisateur (si nécessaire, dépend de ta structure)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relation avec les CartItems (un order peut avoir plusieurs articles)
    public function items()
    {
        return $this->hasMany(CartItem::class, 'order_id');
    }

    // Méthode pour calculer le total d'une commande (si tu veux automatiser cela)
    public function calculateTotal()
    {
        return $this->items->sum(function ($item) {
            return $item->quantity * $item->price;  // Par exemple, en supposant que CartItem a un prix
        });
    }
}
