<?php

namespace App\Livewire;

use App\Models\Property;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.vendor')]
#[Title('My Properties - CoworkLibrary')]
class PropertyManager extends Component
{
    public string $name = '';

    public string $category = 'library';

    public string $address = '';

    public string $description = '';

    public bool $showCreateModal = false;

    /**
     * @var array<string, string>
     */
    protected array $rules = [
        'name' => 'required|min:2|max:255',
        'category' => 'required|in:library,coworking,house,hostel,bus,other',
        'address' => 'nullable|string|max:255',
        'description' => 'nullable|string|max:1000',
    ];

    public function openCreateModal(): void
    {
        $this->reset(['name', 'address', 'description']);
        $this->category = 'library';
        $this->showCreateModal = true;
    }

    public function closeCreateModal(): void
    {
        $this->showCreateModal = false;
        $this->resetValidation();
    }

    public function createProperty(): void
    {
        $this->validate();

        $user = auth()->user();
        if ($user && $user->role === 'customer') {
            $user->update(['role' => 'vendor']);
        }

        $property = Property::create([
            'user_id' => auth()->id(),
            'name' => $this->name,
            'category' => $this->category,
            'address' => $this->address ?: null,
            'description' => $this->description ?: null,
        ]);

        $this->showCreateModal = false;
        $this->reset(['name', 'address', 'description']);

        $this->redirectRoute('spaces.builder', ['property' => $property->id]);
    }

    public function deleteProperty(int $id): void
    {
        $property = Property::where('user_id', auth()->id())->find($id);
        if ($property) {
            $property->delete();
        }
    }

    public function render(): View
    {
        $properties = Property::where('user_id', auth()->id())
            ->withCount([
                'spaces',
                'topLevelSpaces',
            ])
            ->with(['spaces.seats', 'spaces.seatRows'])
            ->latest()
            ->get();

        return view('livewire.property-manager', [
            'properties' => $properties,
        ]);
    }
}
