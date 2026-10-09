<?php

namespace App\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.app')]
#[Title('Kelola Laporan')]
class ManageItems extends ModerationQueue
{
    public function render()
    {
        return view('livewire.admin.manage-items');
    }
}
