<?php

namespace App\Livewire\Admin;

use Livewire\Attributes\Title;

#[Title('Moderasi Laporan')]
class ModerationQueue extends AdminPage
{
    protected function viewName(): string
    {
        return 'livewire.admin.moderation-queue';
    }
}
