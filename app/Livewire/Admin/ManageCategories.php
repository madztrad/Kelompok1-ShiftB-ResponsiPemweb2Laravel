<?php

namespace App\Livewire\Admin;

use Livewire\Attributes\Title;

#[Title('Kelola Kategori')]
class ManageCategories extends AdminPage
{
    public string $name = '';

    public ?int $editingId = null;

    public string $editName = '';

    protected function viewName(): string
    {
        return 'livewire.admin.manage-categories';
    }
}
