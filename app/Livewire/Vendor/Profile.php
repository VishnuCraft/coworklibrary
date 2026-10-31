<?php

namespace App\Livewire\Vendor;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.vendor')]
#[Title('Vendor Profile - CoworkLibrary')]
class Profile extends Component
{
    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public bool $is_whatsapp_same = true;

    public string $whatsapp_number = '';

    public string $current_password = '';

    public string $password = '';

    public string $password_confirmation = '';

    public ?string $statusMessage = null;

    public function mount(): void
    {
        $user = auth()->user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone ?? '';
        $this->whatsapp_number = $user->whatsapp_number ?? '';

        if ($user->whatsapp_number && $user->phone && $user->whatsapp_number !== $user->phone) {
            $this->is_whatsapp_same = false;
        } else {
            $this->is_whatsapp_same = true;
        }
    }

    public function updateProfile(): void
    {
        $user = auth()->user();

        $rules = [
            'name' => 'required|string|min:2|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'phone' => 'nullable|string|min:10|max:20',
        ];

        if (! $this->is_whatsapp_same) {
            $rules['whatsapp_number'] = 'required|string|min:10|max:20';
        }

        $this->validate($rules);

        $cleanPhone = trim($this->phone) ?: null;
        $cleanWhatsapp = $this->is_whatsapp_same ? $cleanPhone : (trim($this->whatsapp_number) ?: null);

        $user->update([
            'name' => $this->name,
            'email' => strtolower(trim($this->email)),
            'phone' => $cleanPhone,
            'whatsapp_number' => $cleanWhatsapp,
        ]);

        $this->statusMessage = 'Profile information updated successfully.';
    }

    public function updatePassword(): void
    {
        $user = auth()->user();

        $this->validate([
            'current_password' => 'required|string',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        if (! Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'The provided password does not match your current password.');

            return;
        }

        $user->update([
            'password' => Hash::make($this->password),
        ]);

        $this->reset(['current_password', 'password', 'password_confirmation']);
        $this->statusMessage = 'Password changed successfully.';
    }

    public function render(): View
    {
        $user = auth()->user();

        $propertiesCount = $user->properties()->count();
        $seatsCount = $user->properties()->with('spaces.seats')->get()->flatMap->spaces->flatMap->seats->where('is_walkway', false)->count();

        return view('livewire.vendor.profile', [
            'user' => $user,
            'propertiesCount' => $propertiesCount,
            'seatsCount' => $seatsCount,
        ]);
    }
}
