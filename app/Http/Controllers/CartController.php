<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CartItem;

class CartController extends Controller
{
    public function index()
    {
        
        $cartItems = CartItem::with('product')
            ->whereNull('ordered_at')
            ->get();

        return response()->json($cartItems);
    }


    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:produits,id',
            'quantity' => 'required|integer|min:1',
        ]);

        CartItem::create($request->all());

        return response()->json(['message' => 'Produit ajouté au panier'], 201);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cartItem = CartItem::findOrFail($id);
        $cartItem->update(['quantity' => $request->quantity]);

        return response()->json(['message' => 'Quantité mise à jour']);
    }

    public function updateOrder(Request $request, $id)
    {
        // Trouver l'article dans le panier
        $cartItem = CartItem::findOrFail($id);

        
        $cartItem->update([
            'ordered_at' => now(), // Enregistre la date et l'heure actuelles
        ]);

        // Retourner une réponse JSON
        return response()->json(['message' => 'Quantité et date de commande mises à jour avec succès']);
    }


    public function destroy($id)
    {
        $cartItem = CartItem::findOrFail($id);
        $cartItem->delete();

        return response()->json(['message' => 'Produit retiré du panier']);
    }

    public function getOrderedItems()
    {
        $orderedItems = CartItem::whereNotNull('ordered_at')
            ->orderBy('ordered_at', 'desc')
            ->get();

        return response()->json($orderedItems);
    }
}
