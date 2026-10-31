<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class UserManager extends Component
{
    public string $search = '';

    public string $roleFilter = 'all'; // 'all', 'customer', 'vendor', 'administrator'

    public string $statusFilter = 'all'; // 'all', 'active', 'inactive'

    public bool $showCreateModal = false;

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public string $role = 'vendor';

    public ?string $feedbackMessage = null;

    /**
     * @var array<string, string>
     */
    protected array $rules = [
        'name' => 'required|string|min:2|max:255',
        'email' => 'required|string|email|max:255|unique:users,email',
        'password' => 'required|string|min:6|confirmed',
        'role' => 'required|in:customer,vendor,administrator',
    ];

    public function openCreateModal(): void
    {
        $this->reset(['name', 'email', 'password', 'password_confirmation']);
        $this->role = match ($this->roleFilter) {
            'administrator' => 'administrator',
            'customer' => 'customer',
            default => 'vendor',
        };
        $this->resetValidation();
        $this->showCreateModal = true;
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->roleFilter = 'all';
        $this->statusFilter = 'all';
    }

    public function setRoleFilter(string $role): void
    {
        $this->roleFilter = in_array($role, ['all', 'customer', 'vendor', 'administrator']) ? $role : 'all';
    }

    public function setStatusFilter(string $status): void
    {
        $this->statusFilter = in_array($status, ['all', 'active', 'inactive']) ? $status : 'all';
    }

    public function closeCreateModal(): void
    {
        $this->showCreateModal = false;
        $this->resetValidation();
    }

    public function createUser(): void
    {
        $this->validate();

        $user = User::create([
            'name' => $this->name,
            'email' => strtolower(trim($this->email)),
            'password' => Hash::make($this->password),
            'role' => $this->role,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $this->showCreateModal = false;
        $this->reset(['name', 'email', 'password', 'password_confirmation']);

        $roleTitle = match ($user->role) {
            'administrator' => 'Administrator',
            'customer' => 'Customer',
            default => 'Vendor',
        };
        $this->feedbackMessage = "{$roleTitle} account for '{$user->name}' was created successfully.";
    }

    public function toggleStatus(int $id): void
    {
        if ($id === auth()->id()) {
            $this->feedbackMessage = 'You cannot disable your own active account.';

            return;
        }

        $user = User::find($id);
        if ($user) {
            $user->is_active = ! $user->is_active;
            $user->save();

            $statusText = $user->is_active ? 'Re-activated' : 'Suspended';
            $this->feedbackMessage = "User {$user->name} is now {$statusText}.";
        }
    }

    public function deleteUser(int $id): void
    {
        if ($id === auth()->id()) {
            $this->feedbackMessage = 'You cannot delete your own account.';

            return;
        }

        $user = User::find($id);
        if (! $user) {
            return;
        }

        if ($user->isAdministrator()) {
            $adminCount = User::whereIn('role', ['administrator', 'super_admin', 'admin'])->count();
            if ($adminCount <= 1) {
                $this->feedbackMessage = 'Cannot delete the sole Administrator account.';

                return;
            }
        }

        $name = $user->name;
        $user->delete();
        $this->feedbackMessage = "User '{$name}' and associated records deleted.";
    }

    public function switchToUser(int $id)
    {
        if ($id === auth()->id()) {
            $this->feedbackMessage = 'You are already logged in as this user.';

            return null;
        }

        $targetUser = User::find($id);
        if (! $targetUser) {
            $this->feedbackMessage = 'User not found.';

            return null;
        }

        if (! $targetUser->is_active) {
            $this->feedbackMessage = 'Cannot switch to a suspended user account.';

            return null;
        }

        if (! session()->has('impersonator_id')) {
            session()->put('impersonator_id', auth()->id());
        }

        Auth::login($targetUser);
        session()->regenerate();

        if ($targetUser->isAdministrator()) {
            return redirect()->route('admin.dashboard');
        }

        if ($targetUser->isVendor()) {
            return redirect()->route('properties.index');
        }

        return redirect()->route('home');
    }

    public function render(): View
    {
        $allCount = User::count();
        $vendorCount = User::where('role', 'vendor')->count();
        $customerCount = User::where('role', 'customer')->count();
        $adminCount = User::whereIn('role', ['administrator', 'super_admin', 'admin'])->count();
        $activeCount = User::where('is_active', true)->count();
        $inactiveCount = User::where('is_active', false)->count();

        $users = User::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('email', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->roleFilter !== 'all', function ($query) {
                if ($this->roleFilter === 'administrator') {
                    $query->whereIn('role', ['administrator', 'super_admin', 'admin']);
                } else {
                    $query->where('role', $this->roleFilter);
                }
            })
            ->when($this->statusFilter === 'active', function ($query) {
                $query->where('is_active', true);
            })
            ->when($this->statusFilter === 'inactive', function ($query) {
                $query->where('is_active', false);
            })
            ->with(['properties.spaces.seats', 'properties.spaces.seatRows', 'bookings'])
            ->withCount('properties')
            ->latest()
            ->get();

        return view('livewire.admin.user-manager', [
            'users' => $users,
            'allCount' => $allCount,
            'vendorCount' => $vendorCount,
            'customerCount' => $customerCount,
            'adminCount' => $adminCount,
            'activeCount' => $activeCount,
            'inactiveCount' => $inactiveCount,
        ]);
    }
}
