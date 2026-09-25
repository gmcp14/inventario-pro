<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Kardex de Inventario</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; color: #333; }
        .header { text-align: center; margin-bottom: 15px; }
        .title { font-size: 16px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 5px; text-align: left; }
        th { background-color: #f0f0f0; text-transform: uppercase; font-size: 9px; }
        .text-center { text-align: center; }
        .badge-in { color: green; font-weight: bold; }
        .badge-out { color: red; font-weight: bold; }
    </style>
</head>
<body>

    <div class="header">
        <div class="title">INVENTARIOPRO - HISTORIAL DE MOVIMIENTOS (KARDEX)</div>
        <div>Fecha de Emisión: {{ date('d/m/Y H:i A') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Código</th>
                <th>Producto</th>
                <th class="text-center">Tipo</th>
                <th class="text-center">Cant.</th>
                <th class="text-center">Stock Ant.</th>
                <th class="text-center">Nuevo Stock</th>
                <th>Descripción / Ref.</th>
                <th>Usuario</th>
            </tr>
        </thead>
        <tbody>
            @foreach($movements as $m)
                <tr>
                    <td>{{ $m->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $m->product->code ?? '-' }}</td>
                    <td>{{ $m->product->name ?? 'N/A' }}</td>
                    <td class="text-center">
                        <span class="{{ $m->type === 'in' ? 'badge-in' : 'badge-out' }}">
                            {{ strtoupper($m->type) }}
                        </span>
                    </td>
                    <td class="text-center">{{ $m->quantity }}</td>
                    <td class="text-center">{{ $m->previous_stock }}</td>
                    <td class="text-center">{{ $m->new_stock }}</td>
                    <td>{{ $m->description }}</td>
                    <td>{{ $m->user->name ?? 'Sistema' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>