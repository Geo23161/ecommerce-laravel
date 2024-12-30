<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Display a listing of the orders for the authenticated user (or all orders if no auth).
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = $request->user(); // Récupérer l'utilisateur authentifié

        if ($user->seller) {
            // Si l'utilisateur est un vendeur, récupérer les commandes liées à ses produits
            $orders = Order::whereHas('items', function ($query) use ($user) {
                $query->whereHas('product', function ($productQuery) use ($user) {
                    $productQuery->where('', $user->seller->id);
                });
            })->get();
        } else {
            // Si l'utilisateur est un simple utilisateur, récupérer ses propres commandes
            $orders = Order::where('user_id', $user->id)->get();
        }

        return response()->json($orders);
    }


    /**
     * Store a new order.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'cart_items' => 'string',
            
            'total' => 'required|numeric|min:0',
        ]);

        // Start the order transaction
        try {
            // Create the order
            $order = Order::create([
                'user_id' => Auth::id(),
                'total' => $request->total,
            ]);

            // Attach the existing cart items to the new order
            foreach (json_decode($request->cart_items) as $item) {
                // Assuming the cart item exists and is already in the 'cart_items' table
                $cartItem = CartItem::findOrFail($item->id);

                // Update the 'order_id' of the existing cart item to associate it with the new order
                if ($cartItem) {
                    $cartItem->update([
                        'order_id' => $order->id,
                        'ordered_at' => now(),
                    ]);
                }
            }

            return response()->json(['message' => 'Order created successfully', 'order' => $order], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to create order', 'error' => $e->getMessage()], 500);
        }
    }
}
