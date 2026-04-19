<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache; // Required for caching
use Exception;

class ProductlistController extends Controller
{
    public function listProducts(Request $request, int $page) 
    {
        $perPage = 5;
        $cacheKey = "products_page_{$page}";
        $cacheTtl = 3600; // Cache for 1 hour (in seconds)

        try {
            $data = Cache::remember($cacheKey, $cacheTtl, function () use ($page, $perPage) {
                $skip = ($page - 1) * $perPage;
                $products = Product::skip($skip)->take($perPage)->get();
                $totalrecords = Product::count();
                $totpage = ceil($totalrecords / $perPage);

                return [
                    'products' => $products,
                    'totalrecords' => $totalrecords,
                    'totpage' => $totpage
                ];
            });

            if ($data['products']->isEmpty()) {
                return response()->json(['message' => 'Product not found.'], 404);
            }

            return response()->json([
                'message' => 'Product Retrieved Successfully.',
                'totalrecords' => $data['totalrecords'],
                'page' => $page,
                'totpage' => $data['totpage'],
                'products' => $data['products']
            ], 200);

        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}