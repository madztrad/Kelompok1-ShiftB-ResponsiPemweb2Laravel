<?php

namespace App\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Masuk')]
class Login extends Component
{
    public string $email = '';

    public string $password = '';

    public string $error = '';

    public function mount()
    {
        if (session('api_token')) {
            return redirect()->route($this->homeRoute());
        }
    }

    public function login()
    {
        $this->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $this->error = '';

        // Kalau validasi lolos, minta Alpine (di browser) memanggil API login.
        $this->dispatch('coba-login');
    }

    // Dipanggil dari browser setelah fetch API login sukses.
    public function simpanToken(string $token, array $user)
    {
        session([
            'api_token' => $token,
            'user' => $user,
        ]);

        return redirect()->route($this->homeRoute($user));
    }

    // Dipanggil dari browser kalau API menjawab error (401/422).
    public function terimaApiError(string $pesan)
    {
        $this->error = $pesan;
    }

    public function render()
    {
        return view('livewire.login');
    }

    /** Admin langsung masuk portal admin, pengguna biasa ke beranda. */
    protected function homeRoute(?array $user = null): string
    {
        return (($user ?? session('user'))['role'] ?? null) === 'admin' ? 'admin' : 'home';
    }
}
