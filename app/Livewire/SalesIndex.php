<?php

namespace App\Livewire;

use App\Models\Sale;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class SalesIndex extends Component
{
    use WithPagination;

    // Filtros
    public $search = '';
    public $dateFrom = '';
    public $dateTo = '';
    public $status = 'all';

    // Modal de Detalle
    public $selectedSale = null;
    public $showDetailModal = false;

    #[Layout('components.layouts.app')]
    public function render()
    {
        $sales = Sale::with(['customer', 'user'])
            ->when($this->search, function ($q) {
                $q->where('voucher_number', 'ilike', '%' . $this->search . '%')
                  ->orWhereHas('customer', function ($query) {
                      $query->where('name', 'ilike', '%' . $this->search . '%')
                            ->orWhere('document_number', 'ilike', '%' . $this->search . '%');
                  });
            })
            ->when($this->dateFrom, function ($q) {
                $q->whereDate('created_at', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function ($q) {
                $q->whereDate('created_at', '<=', $this->dateTo);
            })
            ->when($this->status !== 'all', function ($q) {
                $q->where('status', $this->status);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Métricas rápidas
        $todayTotal = Sale::whereDate('created_at', now())->where('status', 'completed')->sum('total');
        $completedCount = Sale::where('status', 'completed')->count();
        $cancelledCount = Sale::where('status', 'cancelled')->count();

        return view('livewire.sales-index', compact('sales', 'todayTotal', 'completedCount', 'cancelledCount'));
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

   public function showSaleDetails($saleId)
    {
        $this->selectedSale = Sale::with(['customer', 'user', 'details.product'])->find($saleId);
        $this->showDetailModal = true;
    }

    public function closeDetailModal()
    {
        $this->ShowDetailModal = false;
        $this->selectedSale = null;
    }

    public function cancelSale($saleId)
    {
        try{
            DB::transaction(function () use ($saleId){
                $sale = Sale::with('details')->findOrFail($saleId);
                if($sale->status === 'cancelled'){
                   $this->dispatch('swal:error', title: 'Atención', text: 'Esta venta ya se encuentra anulada.');
                    return;
                }

                foreach($Sale->details as $detail){
                    Product::where('id', $detail->product_id)->increment('stock', $detail->quantity);
                }
                $sale->status = 'Cancelled';
                $sales->save();
                Log::info('Venta anulada con éxito', [
                    'sale_id' => $sale->id,
                    'user_id' => auth()->id(),
                ]);
            });
            $this->closeDetailModal();
            $this->dispatch('swal:success', title: '¡Venta Anulada!', text: 'La venta ha sido anulada y el stock fue reincorporado.');
        }catch (\Throwable $e) {
            Log::error('Error al anular venta', ['sale_id' => $saleId, 'error' => $e->getMessage()]);
            $this->dispatch('swal:error', title: 'Error', text: 'No se pudo anular la venta.');
        }
    }
}