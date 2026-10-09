<?php

namespace App\Livewire\My;

use App\Livewire\UserPage;
use Livewire\Attributes\Title;

#[Title('Klaim Saya')]
class MyClaims extends UserPage
{
    /** @return view-string */
    protected function viewName(): string
    {
        return 'livewire.my.my-claims';
    }
}
