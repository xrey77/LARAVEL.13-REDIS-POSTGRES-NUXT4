<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AddproductController extends Controller
{
    public function addProduct(Request $request) {
        $product = Product::where('descriptions', $request->descriptions)->first();
        if ($product) {
            return response()->json(['message' => 'Product Description already exists!'], 404);
        }

        $newProduct = new Product();
        $newProduct->fill($request->all());
        $newProduct->save();

        // 3. Redis Cache Invalidation
        // If you have a 'products_list' cache, clear it now
        Cache::forget('products_all'); 
        
        // Alternatively, if using Cache Tags (Redis only):
        // Cache::tags(['products'])->flush();

        return response()->json(['message' => 'New Product Created Successfully.'], 200);
    }    
}