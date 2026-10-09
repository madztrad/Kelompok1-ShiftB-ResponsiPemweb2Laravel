<?php

namespace App\Livewire\Admin;

use Livewire\Attributes\Title;

#[Title('Kelola Pengguna')]
class ManageUsers extends AdminPage
{
    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $role = 'user';

    public ?int $editingId = null;

    public string $editRole = 'user';

    protected function viewName(): string
    {
        return 'livewire.admin.manage-users';
    }
}
