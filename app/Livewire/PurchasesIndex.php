<?php

namespace App\Livewire;

use App\Models\Purchase;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class PurchasesIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedPurchase = null;
    public $showDetailModal = false;

    #[Layout('components.layouts.app')]
    public function render()
    {
        $purchases = Purchase::with(['supplier', 'user'])
            ->when($this->search, function ($q) {
                $q->where('invoice_number', 'ilike', '%' . $this->search . '%')
                  ->orWhereHas('supplier', function ($query) {
                      $query->where('name', 'ilike', '%' . $this->search . '%');
                  });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.purchases-index', compact('purchases'));
    }

    public function showPurchaseDetails($id)
    {
        $this->selectedPurchase = Purchase::with(['supplier', 'user', 'details.product'])->find($id);
        $this->showDetailModal = true;
    }

    public function closeModal()
    {
        $this->showDetailModal = false;
        $this->selectedPurchase = null;
    }
}