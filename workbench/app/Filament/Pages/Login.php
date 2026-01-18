<?php

namespace App\Filament\Pages;

class Login extends \Filament\Auth\Pages\Login
{
    public function mount(): void
    {
        parent::mount();

        $this->form->fill([
            'email' => 'dev@ffhs.ch',
            'password' => 'password',
            'remember' => true,
        ]);
    }
}
