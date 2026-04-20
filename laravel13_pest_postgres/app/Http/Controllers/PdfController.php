<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;

class PdfController extends Controller
{    
    public function generatePdf()
    {
        $cacheKey = 'product_report_pdf';
        
        $pdfContent = Cache::remember($cacheKey, 3600, function () {
            $products = Product::all();        
            
            $pdf = Pdf::loadView('pdf.product_report', compact('products'))
                      ->setPaper('a4', 'portrait')
                      ->setOptions([
                          'isPhpEnabled' => true,
                          'isRemoteEnabled' => true 
                      ]);

            return $pdf->output();
        });

        return Response::make($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="product_report.pdf"'
        ]);
    }

}


// namespace App\Http\Controllers;

// use App\Models\Product;
// use Illuminate\Http\Request;
// use Barryvdh\DomPDF\Facade\Pdf;

// class PdfController extends Controller
// {    
//     public function generatePdf()
//     {
//         $products = Product::all();        
//         $pdf = Pdf::loadView('pdf.product_report', compact('products'))
//                   ->setPaper('a4', 'portrait')
//                   ->setOptions([
//                       'isPhpEnabled' => true,
//                       'isRemoteEnabled' => true 
//                   ]);

//         return $pdf->stream('product_report.pdf');                  
//     }


// }
