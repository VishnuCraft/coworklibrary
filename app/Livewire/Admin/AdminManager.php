<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class AdminManager extends Component
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

    public function createAdmin(): void
    {
        $this->validate();

        User::create([
            'name' => $this->name,
            'email' => strtolower(trim($this->email)),
            'password' => Hash::make($this->password),
            'role' => 'administrator',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $this->showCreateModal = false;
        $this->reset(['name', 'email', 'password', 'password_confirmation']);
        $this->feedbackMessage = 'Administrator account successfully created.';
    }

    public function toggleStatus(int $id): void
    {
        if ($id === auth()->id()) {
            $this->feedbackMessage = 'You cannot disable your own active administrator account.';

            return;
        }

        $admin = User::whereIn('role', ['administrator', 'super_admin', 'admin'])->find($id);
        if ($admin) {
            $admin->is_active = ! $admin->is_active;
            $admin->save();
            $this->feedbackMessage = "Status for {$admin->name} updated to ".($admin->is_active ? 'Active' : 'Disabled').'.';
        }
    }

    public function deleteAdmin(int $id): void
    {
        if ($id === auth()->id()) {
            $this->feedbackMessage = 'You cannot delete your own account.';

            return;
        }

        $adminCount = User::whereIn('role', ['administrator', 'super_admin', 'admin'])->count();
        if ($adminCount <= 1) {
            $this->feedbackMessage = 'Cannot delete the sole Administrator account.';

            return;
        }

        $admin = User::whereIn('role', ['administrator', 'super_admin', 'admin'])->find($id);
        if ($admin) {
            $name = $admin->name;
            $admin->delete();
            $this->feedbackMessage = "Administrator {$name} deleted.";
        }
    }

    public function render(): View
    {
        $admins = User::whereIn('role', ['administrator', 'super_admin', 'admin'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('email', 'like', '%'.$this->search.'%');
                });
            })
            ->latest()
            ->get();

        return view('livewire.admin.admin-manager', [
            'admins' => $admins,
        ]);
    }
}
