<?php

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * Dasar halaman untuk pengguna yang sudah login (bukan portal admin).
 * Mengarahkan ke halaman login bila belum ada token di session.
 */
#[Layout('components.layouts.app')]
abstract class UserPage extends Component
{
    public function mount(): void
    {
        if (! session('api_token')) {
            $this->redirect(route('login'), navigate: true);
        }
    }

    /** @return view-string */
    abstract protected function viewName(): string;

    public function render(): View
    {
        return view($this->viewName());
    }
}
