<?php

namespace App\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Moderasi Laporan')]
class ModerationQueue extends Component
{
    public function mount()
    {
        if (! session('api_token')) {
            return redirect()->route('login');
        }

        if (session('user.role') !== 'admin') {
            return redirect()->route('home');
        }
    }

    public function render()
    {
        return view('livewire.admin.moderation-queue');
    }
}
