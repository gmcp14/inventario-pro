<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Product;
use App\Models\InventoryMovement;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    // 1. Factura / Comprobante de Venta
    public function saleInvoice($id)
    {
        $sale = Sale::with(['customer', 'user', 'details.product'])->findOrFail($id);

        $pdf = Pdf::loadView('reports.sale-invoice', compact('sale'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream("Factura_Venta_{$sale->id}.pdf");
    }

    // 2. Comprobante de Compra
    public function purchaseReceipt($id)
    {
        $purchase = Purchase::with(['supplier', 'user', 'details.product'])->findOrFail($id);

        $pdf = Pdf::loadView('reports.purchase-receipt', compact('purchase'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream("Comprobante_Compra_{$purchase->invoice_number}.pdf");
    }

    // 3. Reporte de Kardex / Movimientos
    public function kardexReport(Request $request)
    {
        $movements = InventoryMovement::with(['product', 'user'])
            ->orderBy('id', 'desc')
            ->get();

        $pdf = Pdf::loadView('reports.kardex-pdf', compact('movements'))
            ->setPaper('a4', 'landscape'); // Horizontal para mayor espacio de columnas

        return $pdf->stream("Reporte_Kardex_" . date('Y-m-d') . ".pdf");
    }

    // 4. Reporte de Stock e Inventario Actual
    public function inventoryReport()
    {
        $products = Product::with('category')
            ->where('is_active', true)
            ->orderBy('name', 'asc')
            ->get();

        $pdf = Pdf::loadView('reports.inventory-pdf', compact('products'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream("Inventario_Actual_" . date('Y-m-d') . ".pdf");
    }
}