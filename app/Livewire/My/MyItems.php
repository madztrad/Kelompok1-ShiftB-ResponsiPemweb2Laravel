<?php

namespace App\Livewire\My;

use App\Livewire\UserPage;
use Livewire\Attributes\Title;

#[Title('Laporan Saya')]
class MyItems extends UserPage
{
    /** @return view-string */
    protected function viewName(): string
    {
        return 'livewire.my.my-items';
    }
}
