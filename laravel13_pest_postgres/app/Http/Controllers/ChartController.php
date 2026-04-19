<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class ChartController extends Controller
{
    public function generateChart(): JsonResponse 
    {
        // Cache for 1 hour (3600 seconds)
        $salesData = Cache::remember('sales_chart_data', 3600, function () {
            $sales = Sale::all();

            if ($sales->isEmpty()) {
                return null;
            }

            return $sales->map(fn($sale) => [
                'salesamount' => $sale->salesamount,
                'salesdate' => $sale->salesdate
            ]);
        });

        if (!$salesData) {
            return response()->json(['message' => 'Sales data not found.'], 404);
        }

        return response()->json($salesData, 200);
    }
}

// namespace App\Http\Controllers;

// use App\Models\Sale;
// use Illuminate\Http\Request;
// use Illuminate\Http\JsonResponse;

// class ChartController extends Controller
// {
//     public function generateChart(): JsonResponse 
//     {
//         $sales = Sale::all();

//         if ($sales->isEmpty()) {
//             return response()->json(['message' => 'Sales data not found.'], 404);
//         }

//         $salesData = $sales->map(fn($sale) => [
//             'salesamount' => $sale->salesamount,
//             'salesdate' => $sale->salesdate
//         ]);

//         return response()->json($salesData, 200);
//     }
// }
