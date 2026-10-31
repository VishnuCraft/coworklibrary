<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.guest')]
class Login extends Component
{
    public string $email = '';

    public string $password = '';

    public bool $remember = false;

    /**
     * @var array<string, string>
     */
    protected array $rules = [
        'email' => 'required|email',
        'password' => 'required',
    ];

    public function login(): void
    {
        $this->validate();

        $user = User::where('email', strtolower(trim($this->email)))->first();

        if (! $user || ! Hash::check($this->password, $user->password)) {
            $this->addError('email', 'These credentials do not match our administrative records.');

            return;
        }

        if (! $user->isAdmin()) {
            $this->addError('email', 'Access Denied: This portal is strictly for administrators.');

            return;
        }

        if (! $user->is_active) {
            $this->addError('email', 'Your administrator account has been disabled. Contact Super Admin.');

            return;
        }

        Auth::login($user, $this->remember);
        session()->regenerate();

        $this->redirectRoute('admin.dashboard', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.admin.login');
    }
}
