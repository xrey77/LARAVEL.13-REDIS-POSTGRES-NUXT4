<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProductListController extends Controller
{

    public function listProducts(Request $request, ?int $page) 
    {
        $perPage = 5;
        $cacheKey = "products_page_{$page}";

        try {
            $data = Cache::remember($cacheKey, 3600, function () use ($page, $perPage) {
                $products = Product::skip(($page - 1) * $perPage)
                                    ->take($perPage)
                                    ->get()
                                    ->toArray();

                $totalRecords = Product::count();

                return [
                    'products' => $products,
                    'totalrecords' => $totalRecords,
                    'totpage' => (int) ceil($totalRecords / $perPage)
                ];
            });

            if (empty($data['products'])) {
                return response()->json(['message' => 'No products found.', 'products' => []], 404);
            }

            return response()->json([
                'message' => 'Product Retrieved Successfully.',
                'totalrecords' => $data['totalrecords'],
                'page' => $page,
                'totpage' => $data['totpage'],
                'products' => $data['products']
            ], 200);

        } catch (\Exception $e) {
            \Log::error("Cache error: " . $e->getMessage());
            return response()->json(['error' => 'Server Error'], 500);
        }
    }

    // public function listProducts(Request $request, int $page) 
    // {
    //     $perPage = 5;
    //     $cacheKey = "products_page_{$page}";
    //     $cacheTtl = 3600; // Cache for 1 hour (in seconds)

    //     try {
    //         $data = Cache::remember($cacheKey, $cacheTtl, function () use ($page, $perPage) {
    //             $skip = ($page - 1) * $perPage;
    //             $products = Product::skip($skip)->take($perPage)->get();
    //             $totalrecords = Product::count();
    //             $totpage = ceil($totalrecords / $perPage);

    //             return [
    //                 'products' => $products,
    //                 'totalrecords' => $totalrecords,
    //                 'totpage' => $totpage
    //             ];
    //         });

    //         if ($data['products']->isEmpty()) {
    //             return response()->json([
    //                 'message' => 'No products found for this page.',
    //                 'products' => [],
    //                 'totalrecords' => $data['totalrecords']
    //             ], 200);
    //         }            

    //         return response()->json([
    //             'message' => 'Product Retrieved Successfully.',
    //             'totalrecords' => $data['totalrecords'],
    //             'page' => $page,
    //             'totpage' => $data['totpage'],
    //             'products' => $data['products']
    //         ], 200);

    //     } catch (\Exception $e) {
    //         return response()->json(['message' => $e->getMessage()], 500);
    //     }
    // }
}