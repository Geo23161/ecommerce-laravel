<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Seller;
use App\Models\Buyer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class JWTAuthController extends Controller
{
    // User registration
    public function register(Request $request)
    {



        $validator = Validator::make($request->all(), [
            'type' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'kkiapayId' => 'required_if:type,vendor|string|nullable|max:255', 
        ]);
        
        if($validator->fails()){
           return response()->json($validator->errors()->toJson(), 400);
        }

        $user = User::create([
            'name' => "User",
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
        ]);

        if ($request->get('type') == 'vendor') {
            
            $seller = Seller::create([
                'user_id' => $user->id,  
                'store_name' => $request->get('store_name', "Default"),
                'store_address' => $request->get('store_address', "Default"),
                'id_kkiapay' => $request->get('kkiapayId', "Default"),  
            ]);
        } else {
            // Crée un Buyer
            $buyer = Buyer::create([
                'user_id' => $user->id,  // Associe le buyer à l'utilisateur
                'shipping_address' => $request->get('shipping_address', "Default"),  // Adresse de livraison pour le buyer
            ]);
        }

        $token = JWTAuth::fromUser($user);

        

        return response()->json([
            'token' => $token,
        ], 201);
    }

  
    public function login(Request $request)
    {

        $credentials = $request->only('email', 'password');
        $type = $request->input('type');

        try {
            
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'Invalid credentials'], 401);
            }


            $user = auth()->user();

            
            if ($type == 'vendor' && !$user->seller) {

                return response()->json(['error' => 'User is not a vendor'], 400);
            }

            if ($type == 'buyer' && $user->seller) {
                
                return response()->json(['error' => 'User is not a buyer'], 400);
            }

            return response()->json([
                'token' => $token
            ]);
            
        } catch (JWTException $e) {
            return response()->json(['error' => 'Could not create token'], 500);
        }
    }


    // Get authenticated user
    public function getUser()
    {
        try {
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['error' => 'User not found'], 404);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Invalid token'], 400);
        }

        return response()->json(compact('user'));
    }

    // User logout
    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function refreshToken(Request $request)
    {
        try {
            
            $currentToken = JWTAuth::getToken();

            
            $newToken = JWTAuth::refresh($currentToken);

            

            return response()->json([
                'success' => true,
                'token' => $newToken
            ], 200);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token invalide ou expiré'
            ], 401);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du rafraîchissement du token'
            ], 500);
        }
    }

}