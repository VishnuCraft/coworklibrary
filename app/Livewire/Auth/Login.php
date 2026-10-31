<?php

namespace App\Livewire\Auth;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Login extends Component
{
    public string $email = '';

    public string $password = '';

    public bool $remember = false;

    /**
     * @var array<string, string>
     */
    protected array $rules = [
        'email' => 'required|string|email',
        'password' => 'required|string',
    ];

    public function login(): void
    {
        $this->validate();

        if (Auth::attempt(['email' => strtolower(trim($this->email)), 'password' => $this->password], $this->remember)) {
            session()->regenerate();

            $user = Auth::user();
            if ($user->isVendor()) {
                $this->redirectIntended(default: route('owner.dashboard'), navigate: true);
            } elseif ($user->isAdministrator()) {
                $this->redirectIntended(default: route('admin.dashboard'), navigate: true);
            } else {
                $this->redirectIntended(default: route('customer.bookings'), navigate: true);
            }

            return;
        }

        $this->addError('email', 'These credentials do not match our records.');
    }

    public function render(): View
    {
        return view('livewire.auth.login');
    }
}
