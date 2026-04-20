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
        $data = Cache::remember('products_by_category', 3600, function () {
            return Product::all()->groupBy('category');
        });

        // Force check: If it's a string, it's a cache serialization error
        if (is_string($data)) {
            $data = json_decode($data);
        }

        $pdf = Pdf::loadView('reports.products', ['data' => $data])
                ->setPaper('a4', 'portrait')
                ->setOptions([
                    'isPhpEnabled' => true,
                    'isRemoteEnabled' => true 
                ]);

        // return $pdf->stream('products_report.pdf');
        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="products.pdf"',
        ]);

    }


    // public function generateCategoryReport()
    // {
    //     $data = Cache::remember('products_by_category', 3600, function () {
    //         return Product::all()->groupBy('category');
    //     });

    //     $pdf = Pdf::loadView('reports.products', compact('data'))
    //             ->setPaper('a4', 'portrait')
    //             ->setOptions([
    //                 'isPhpEnabled' => true,
    //                 'isRemoteEnabled' => true 
    //             ]);

    //     // Output the actual PDF binary content
    //     return response($pdf->output(), 200, [
    //         'Content-Type' => 'application/pdf',
    //         'Content-Disposition' => 'inline; filename="products_report.pdf"',
    //     ]);
    // }


    // public function generateCategoryReport()
    // {
    //     $data = Cache::remember('products_by_category', 3600, function () {
    //         return Product::all()->groupBy('category');
    //     });

    //     $pdf = Pdf::loadView('reports.products', compact('data'))
    //               ->setPaper('a4', 'portrait')
    //               ->setOptions([
    //                   'isPhpEnabled' => true,
    //                   'isRemoteEnabled' => true 
    //               ]);

    //     return Response::make($data, 200, [
    //         'Content-Type' => 'application/pdf',
    //         'Content-Disposition' => 'inline; filename="reports.products.pdf"'
    //     ]);
    // }
}
