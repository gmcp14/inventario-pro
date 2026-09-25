<?php

namespace App\Livewire;

use Illuminate\Http\Request;
use Livewire\Attributes\Layout;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Dashboard extends Component
{
    /**
     * Muestra la vista del panel principal aplicando el Layout.
     */
    #[Layout('components.layouts.app')]
    public function render()
    {
        // 1. Tarjetas KPIs principales
        $todaySales = Sale::whereDate('created_at', now())->where('status', 'completed')->sum('total');
        $monthSales = Sale::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('status', 'completed')
            ->sum('total');

        $monthPurchases = Purchase::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('status', 'completed')
            ->sum('total');

        $lowStockCount = Product::where('is_active', true)->whereColumn('stock', '<=', 'min_stock')->count();
        $totalCustomers = Customer::where('is_active', true)->count();

        // 2. Datos para el gráfico de Ventas vs Compras (Últimos 7 días)
        $last7Days = collect(range(6, 0))->map(function ($days) {
            $date = now()->subDays($days);
            
            $sales = Sale::whereDate('created_at', $date->format('Y-m-d'))
                ->where('status', 'completed')
                ->sum('total');

            $purchases = Purchase::whereDate('created_at', $date->format('Y-m-d'))
                ->where('status', 'completed')
                ->sum('total');

            return [
                'day' => $date->isoFormat('ddd D/M'),
                'sales' => (float) $sales,
                'purchases' => (float) $purchases,
            ];
        });

        // 3. Productos más vendidos (Top 5)
        $topProducts = DB::table('sale_details')
            ->join('products', 'sale_details.product_id', '=', 'products.id')
            ->select('products.name', DB::raw('SUM(sale_details.quantity) as total_qty'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();

        // 4. Últimas 5 ventas registradas
        $recentSales = Sale::with('customer')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // 5. Productos con Stock Bajo
        $lowStockProducts = Product::where('is_active', true)
            ->whereColumn('stock', '<=', 'min_stock')
            ->orderBy('stock', 'asc')
            ->take(5)
            ->get();

        return view('livewire.dashboard', compact(
            'todaySales',
            'monthSales',
            'monthPurchases',
            'lowStockCount',
            'totalCustomers',
            'last7Days',
            'topProducts',
            'recentSales',
            'lowStockProducts'
        ));
    }
}
