<?php

namespace App\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * Dasar semua halaman portal admin: guard session + layout bersama.
 */
#[Layout('components.layouts.app')]
abstract class AdminPage extends Component
{
    public string $notice = '';

    public string $error = '';

    public function mount(): void
    {
        if (! session('api_token')) {
            $this->redirect(route('login'), navigate: true);

            return;
        }

        if (session('user.role') !== 'admin') {
            $this->redirect(route('home'), navigate: true);
        }
    }

    public function render()
    {
        return view($this->viewName());
    }

    abstract protected function viewName(): string;
}
