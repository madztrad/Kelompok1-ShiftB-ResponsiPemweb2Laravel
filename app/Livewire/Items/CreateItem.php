<?php

namespace App\Livewire\Items;

use App\Livewire\UserPage;
use Livewire\Attributes\Title;

#[Title('Lapor Barang')]
class CreateItem extends UserPage
{
    /** @return view-string */
    protected function viewName(): string
    {
        return 'livewire.items.create-item';
    }
}
