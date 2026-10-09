<?php

namespace App\Livewire\Admin;

use Livewire\Attributes\Title;

#[Title('Dashboard Admin')]
class Dashboard extends AdminPage
{
    protected function viewName(): string
    {
        return 'livewire.admin.dashboard';
    }
}
