<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProductsearchController extends Controller
{
    public function productSearch(Request $request, int $page, string $key) {
        $perPage = 5;
        $skip = ($page - 1) * $perPage;
        

        $safeKey = Str::slug($key);
        $cacheKey = "products_search_{$safeKey}_page_{$page}";

        // $cacheKey = "products_search_{$key}_page_{$page}";

        try {
            $data = Cache::remember($cacheKey, 600, function () use ($key, $skip, $perPage, $page) {
                $query = Product::where('descriptions', 'ILIKE', '%' . $key . '%');
                $totalrecords = $query->count();
                $products = $query->skip($skip)->take($perPage)->get();

                if ($products->isEmpty()) {
                    return null;
                }

                return [
                    'page' => $page,
                    'totpage' => ceil($totalrecords / $perPage),
                    'totalrecords' => $totalrecords,
                    'products' => $products->toArray()
                ];
            });

            if (!$data) {
                return response()->json(['message' => 'Product(s) not found.'], 404);
            }
            
            return response()->json(array_merge(['message' => 'Searched found..'], $data), 200);

        } catch(\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }    
}