<?php

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * Halaman beranda publik yang menyesuaikan diri:
 * - pengguna yang sudah login melihat dashboard ringkasan,
 * - tamu melihat landing page (marketing).
 */
#[Layout('components.layouts.app')]
#[Title('Beranda')]
class Home extends Component
{
    public function render(): View
    {
        return view('livewire.home');
    }
}
