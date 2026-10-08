<?php

namespace App\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Daftar')]
class Register extends Component
{
    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function mount()
    {
        if (session('api_token')) {
            return redirect()->route('home');
        }
    }

    public function register()
    {
        $this->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email'],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        // Kalau validasi lolos, minta Alpine (di browser) memanggil API register.
        $this->dispatch('coba-daftar');
    }

    // Dipanggil dari browser setelah fetch API register sukses.
    public function simpanToken(string $token, array $user)
    {
        session([
            'api_token' => $token,
            'user' => $user,
        ]);

        return redirect()->route('home');
    }

    // Dipanggil dari browser kalau API menjawab error.
    // Balasan 422 berbentuk: errors => ['email' => ['pesan', ...]]
    public function terimaApiError(array $errors)
    {
        if (empty($errors)) {
            $this->addError('email', 'Registrasi gagal, coba lagi.');

            return;
        }

        foreach ($errors as $field => $messages) {
            $this->addError($field, is_array($messages) ? $messages[0] : $messages);
        }
    }

    public function render()
    {
        return view('livewire.register');
    }
}
