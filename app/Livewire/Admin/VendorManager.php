<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class VendorManager extends Component
{
    public string $search = '';

    public bool $showCreateModal = false;

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public ?string $feedbackMessage = null;

    /**
     * @var array<string, string>
     */
    protected array $rules = [
        'name' => 'required|string|min:2|max:255',
        'email' => 'required|string|email|max:255|unique:users,email',
        'password' => 'required|string|min:6|confirmed',
    ];

    public function openCreateModal(): void
    {
        $this->reset(['name', 'email', 'password', 'password_confirmation']);
        $this->resetValidation();
        $this->showCreateModal = true;
    }

    public function closeCreateModal(): void
    {
        $this->showCreateModal = false;
        $this->resetValidation();
    }

    public function createVendor(): void
    {
        $this->validate();

        $vendor = User::create([
            'name' => $this->name,
            'email' => strtolower(trim($this->email)),
            'password' => Hash::make($this->password),
            'role' => 'vendor',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $this->showCreateModal = false;
        $this->reset(['name', 'email', 'password', 'password_confirmation']);
        $this->feedbackMessage = "Vendor account for '{$vendor->name}' created successfully.";
    }

    public function toggleStatus(int $id): void
    {
        $vendor = User::where('role', 'vendor')->find($id);
        if ($vendor) {
            $vendor->is_active = ! $vendor->is_active;
            $vendor->save();

            $statusText = $vendor->is_active ? 'Re-activated' : 'Suspended';
            $this->feedbackMessage = "Vendor {$vendor->name} is now {$statusText}.";
        }
    }

    public function deleteVendor(int $id): void
    {
        $vendor = User::where('role', 'vendor')->find($id);
        if ($vendor) {
            $name = $vendor->name;
            $vendor->delete();
            $this->feedbackMessage = "Vendor {$name} and associated data deleted.";
        }
    }

    public function switchToVendor(int $id)
    {
        if ($id === auth()->id()) {
            return null;
        }

        $vendor = User::find($id);
        if (! $vendor || ! $vendor->is_active) {
            $this->feedbackMessage = 'Vendor account is not active or could not be found.';

            return null;
        }

        if (! session()->has('impersonator_id')) {
            session()->put('impersonator_id', auth()->id());
        }

        Auth::login($vendor);
        session()->regenerate();

        return redirect()->route('properties.index');
    }

    public function render(): View
    {
        $vendors = User::where('role', 'vendor')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('email', 'like', '%'.$this->search.'%');
                });
            })
            ->with(['properties.spaces.seats', 'properties.spaces.seatRows'])
            ->withCount('properties')
            ->latest()
            ->get();

        return view('livewire.admin.vendor-manager', [
            'vendors' => $vendors,
        ]);
    }
}
