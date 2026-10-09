<?php

namespace App\Livewire\Items;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * Katalog barang publik: menelusuri laporan yang sudah disetujui admin.
 * Data diambil dari API (Jalur B) lewat helper window.api, bukan akses DB langsung.
 */
#[Layout('components.layouts.app')]
#[Title('Katalog Barang')]
class BrowseItems extends Component
{
    public function render(): View
    {
        return view('livewire.items.browse-items');
    }
}
