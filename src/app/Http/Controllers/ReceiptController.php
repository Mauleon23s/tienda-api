<?php

namespace App\Http\Controllers;

use App\Models\Receipt;
use Barryvdh\DomPDF\Facade\Pdf;

class ReceiptController extends Controller
{
    public function show(Receipt $receipt)
    {
        return response()->json($receipt);
    }

    public function pdf(Receipt $receipt)
    {
        $pdf = Pdf::loadView('receipts.pdf', [
            'receipt' => $receipt
        ]);

        return $pdf->download("receipt_{$receipt->id}.pdf");
    }
}