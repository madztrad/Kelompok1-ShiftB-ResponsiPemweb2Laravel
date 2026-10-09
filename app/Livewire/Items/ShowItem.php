<?php

namespace App\Livewire\Items;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * Detail sebuah laporan barang + form pengajuan klaim.
 * Data diambil dari API (Jalur B) lewat helper window.api.
 */
#[Layout('components.layouts.app')]
#[Title('Detail Barang')]
class ShowItem extends Component
{
    public int $itemId;

    public function mount(int $item): void
    {
        $this->itemId = $item;
    }

    public function render(): View
    {
        return view('livewire.items.show-item');
    }
}
