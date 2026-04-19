<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Cache; //

class ProductbycategoryController extends Controller
{
    public function generateCategoryReport()
    {
        // Cache data for 60 minutes (3600 seconds)
        $data = Cache::remember('products_by_category', 3600, function () {
            return Product::all()->groupBy('category');
        });

        $pdf = Pdf::loadView('reports.products', compact('data'))
                  ->setPaper('a4', 'portrait')
                  ->setOptions([
                      'isPhpEnabled' => true,
                      'isRemoteEnabled' => true 
                  ]);

        return $pdf->download('product-report.pdf');
    }
}

// namespace App\Http\Controllers;

// use Illuminate\Http\Request;
// use App\Models\Product;
// use Barryvdh\DomPDF\Facade\Pdf;

// class ProductbycategoryController extends Controller
// {
//     public function generateCategoryReport()
//     {
//         $data = Product::all()->groupBy('category');
//         $pdf = Pdf::loadView('reports.products', compact('data'))
//                   ->setPaper('a4', 'portrait')
//                   ->setOptions([
//                       'isPhpEnabled' => true,
//                       'isRemoteEnabled' => true 
//                   ]);

//            return $pdf->download('product-report.pdf');
//     }
    
// }
