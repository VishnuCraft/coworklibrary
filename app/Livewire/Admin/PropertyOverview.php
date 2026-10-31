<?php

namespace App\Livewire\Admin;

use App\Models\Property;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class PropertyOverview extends Component
{
    public string $search = '';

    public string $selectedCategory = '';

    public ?string $feedbackMessage = null;

    public function deleteProperty(int $id): void
    {
        $property = Property::find($id);
        if ($property) {
            $name = $property->name;
            $property->delete();
            $this->feedbackMessage = "Property '{$name}' has been deleted.";
        }
    }

    public function render(): View
    {
        $properties = Property::with(['user', 'spaces.seats', 'spaces.seatRows'])
            ->withCount('topLevelSpaces')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('address', 'like', '%'.$this->search.'%')
                        ->orWhereHas('user', function ($uq) {
                            $uq->where('name', 'like', '%'.$this->search.'%')
                                ->orWhere('email', 'like', '%'.$this->search.'%');
                        });
                });
            })
            ->when($this->selectedCategory, function ($query) {
                $query->where('category', $this->selectedCategory);
            })
            ->latest()
            ->get();

        return view('livewire.admin.property-overview', [
            'properties' => $properties,
        ]);
    }
}
