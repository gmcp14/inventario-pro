<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factura de Venta #{{ $sale->id }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; }
        .title { font-size: 18px; font-weight: bold; text-transform: uppercase; }
        .info-table, .details-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .info-table td { padding: 4px; }
        .details-table th, .details-table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .details-table th { background-color: #f4f4f4; text-transform: uppercase; font-size: 10px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-box { font-size: 14px; font-weight: bold; }
    </style>
</head>
<body>

    <div class="header">
        <div class="title">InventarioPro</div>
        <div>Comprobante de Venta #{{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}</div>
        <div>Fecha: {{ $sale->created_at->format('d/m/Y H:i A') }}</div>
    </div>

    <table class="info-table">
        <tr>
            <td><strong>Cliente:</strong> {{ $sale->customer->name ?? 'Público General' }}</td>
            <td><strong>Atendido por:</strong> {{ $sale->user->name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td><strong>Doc / Cédula:</strong> {{ $sale->customer->document_number ?? 'N/A' }}</td>
            <td><strong>Método de Pago:</strong> {{ strtoupper($sale->payment_method ?? 'Efectivo') }}</td>
        </tr>
    </table>

    <table class="details-table">
        <thead>
            <tr>
                <th>Código</th>
                <th>Producto</th>
                <th class="text-center">Cant.</th>
                <th class="text-right">P. Unitario</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->details as $item)
                <tr>
                    <td>{{ $item->product->code ?? 'N/A' }}</td>
                    <td>{{ $item->product->name ?? 'Producto no disponible' }}</td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">${{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-right">${{ number_format($item->subtotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table style="width: 100%;">
        <tr>
            <td style="width: 50%;"></td>
            <td style="width: 50%;">
                <table class="info-table">
                    <tr>
                        <td class="text-right"><strong>Subtotal:</strong></td>
                        <td class="text-right">${{ number_format($sale->subtotal ?? $sale->total, 2) }}</td>
                    </tr>
                    <tr class="total-box">
                        <td class="text-right"><strong>Total A Pagar:</strong></td>
                        <td class="text-right">${{ number_format($sale->total, 2) }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</body>
</html>