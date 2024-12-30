<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produit;
use Illuminate\Support\Facades\Auth;

class ProduitController extends Controller
{
    /**
     * Affiche la liste des produits.
     */
    public function index(Request $request)
    {
        $user = $request->user(); // Récupérer l'utilisateur authentifié

        if ($user->seller) {
            $produits = Produit::where('seller_id', $user->seller->id)->get();
        } else {
            
            $produits = Produit::all();
        }

        return response()->json($produits);
    }


    /**
     * Affiche un produit spécifique.
     */
    public function show($id)
    {
        $produit = Produit::find($id);

        if (!$produit) {
            return response()->json(['message' => 'Produit non trouvé'], 404);
        }

        return response()->json($produit);
    }

    /**
     * Ajoute un nouveau produit (réservé aux vendeurs).
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // Vérifier si l'utilisateur est un vendeur
        if (!$user->seller) {
            return response()->json(['message' => 'Accès refusé : seuls les vendeurs peuvent ajouter des produits'], 403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'rating' => 'nullable|numeric|min:0|max:5',
            'discount' => 'nullable|numeric|min:0|max:100',
        ]);

        $produit = Produit::create(array_merge($request->all(), ['seller_id' => $user->seller->id, 'quantite' => 10]));

        return response()->json(['message' => 'Produit ajouté avec succès', 'produit' => $produit], 201);
    }

    /**
     * Met à jour un produit existant (réservé aux vendeurs).
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();

        // Vérifier si l'utilisateur est un vendeur
        if (!$user->seller) {
            return response()->json(['message' => 'Accès refusé : seuls les vendeurs peuvent modifier des produits'], 403);
        }

        $produit = Produit::find($id);

        if (!$produit) {
            return response()->json(['message' => 'Produit non trouvé'], 404);
        }

        // Vérifier si le produit appartient au vendeur connecté
        if ($produit->seller_id !== $user->seller->id) {
            return response()->json(['message' => 'Accès refusé : vous ne pouvez modifier que vos propres produits'], 403);
        }

        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'price' => 'sometimes|required|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'rating' => 'sometimes|nullable|numeric|min:0|max:5',
            'discount' => 'nullable|numeric|min:0|max:100',
        ]);

        $produit->update($request->all());

        return response()->json(['message' => 'Produit mis à jour avec succès', 'produit' => $produit]);
    }

    /**
     * Supprime un produit (réservé aux vendeurs).
     */
    public function destroy($id)
    {
        $user = Auth::user();

        // Vérifier si l'utilisateur est un vendeur
        if (!$user->seller) {
            return response()->json(['message' => 'Accès refusé : seuls les vendeurs peuvent supprimer des produits'], 403);
        }

        $produit = Produit::find($id);

        if (!$produit) {
            return response()->json(['message' => 'Produit non trouvé'], 404);
        }

        // Vérifier si le produit appartient au vendeur connecté
        if ($produit->seller_id !== $user->seller->id) {
            return response()->json(['message' => 'Accès refusé : vous ne pouvez supprimer que vos propres produits'], 403);
        }

        $produit->delete();

        return response()->json(['message' => 'Produit supprimé avec succès']);
    }
}
