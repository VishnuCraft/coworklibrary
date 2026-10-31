<?php

namespace App\Livewire\Vendor;

use App\Models\Property;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.vendor')]
#[Title('Vendor Dashboard - CoworkLibrary')]
class Dashboard extends Component
{
    public string $newPropertyName = '';

    public string $newPropertyCategory = 'library';

    public string $newPropertyAddress = '';

    public string $newPropertyDescription = '';

    public bool $showCreateModal = false;

    /**
     * @var array<string, string>
     */
    protected array $rules = [
        'newPropertyName' => 'required|min:2|max:255',
        'newPropertyCategory' => 'required|in:library,coworking,house,hostel,bus,other',
        'newPropertyAddress' => 'nullable|string|max:255',
        'newPropertyDescription' => 'nullable|string|max:1000',
    ];

    public function openCreateModal(): void
    {
        $this->reset(['newPropertyName', 'newPropertyAddress', 'newPropertyDescription']);
        $this->newPropertyCategory = 'library';
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
            'name' => $this->newPropertyName,
            'category' => $this->newPropertyCategory,
            'address' => $this->newPropertyAddress ?: null,
            'description' => $this->newPropertyDescription ?: null,
        ]);

        $this->showCreateModal = false;
        $this->reset(['newPropertyName', 'newPropertyAddress', 'newPropertyDescription']);

        $this->redirectRoute('spaces.builder', ['property' => $property->id]);
    }

    public function render(): View
    {
        $userId = auth()->id();

        /** @var Collection<int, Property> $properties */
        $properties = Property::where('user_id', $userId)
            ->withCount(['spaces', 'topLevelSpaces'])
            ->with(['spaces.seats', 'spaces.seatRows'])
            ->latest()
            ->get();

        $totalProperties = $properties->count();
        $totalFloors = $properties->sum('top_level_spaces_count');

        $allSeats = $properties->flatMap->spaces->flatMap->seats;
        $realSeats = $allSeats->where('is_walkway', false);
        $totalBookableSeats = $realSeats->count();
        $totalWalkways = $allSeats->where('is_walkway', true)->count();

        $availableSeats = $realSeats->where('status', 'available')->count();
        $occupiedSeats = $realSeats->where('status', 'occupied')->count();
        $maintenanceSeats = $realSeats->where('status', 'maintenance')->count();

        $occupancyRate = $totalBookableSeats > 0
            ? round(($occupiedSeats / $totalBookableSeats) * 100, 1)
            : 0;

        // Categories breakdown
        $categoryCounts = [
            'library' => $properties->where('category', 'library')->count(),
            'coworking' => $properties->where('category', 'coworking')->count(),
            'hostel' => $properties->whereIn('category', ['house', 'hostel'])->count(),
            'bus' => $properties->where('category', 'bus')->count(),
            'other' => $properties->where('category', 'other')->count(),
        ];

        return view('livewire.vendor.dashboard', [
            'properties' => $properties,
            'totalProperties' => $totalProperties,
            'totalFloors' => $totalFloors,
            'totalBookableSeats' => $totalBookableSeats,
            'totalWalkways' => $totalWalkways,
            'availableSeats' => $availableSeats,
            'occupiedSeats' => $occupiedSeats,
            'maintenanceSeats' => $maintenanceSeats,
            'occupancyRate' => $occupancyRate,
            'categoryCounts' => $categoryCounts,
        ]);
    }
}
