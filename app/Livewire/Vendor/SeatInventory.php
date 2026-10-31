<?php

namespace App\Livewire\Vendor;

use App\Models\Property;
use App\Models\Seat;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.vendor')]
#[Title('Seat Inventory - CoworkLibrary')]
class SeatInventory extends Component
{
    use WithPagination;

    public ?int $selectedPropertyId = null;

    public string $statusFilter = 'all';

    public string $search = '';

    public ?int $editingSeatId = null;

    public string $editSeatNumber = '';

    public string $editSeatStatus = 'available';

    public ?string $editSeatPrice = null;

    public ?string $editSeatNotes = null;

    public bool $showEditModal = false;

    public function updatedSelectedPropertyId(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updateSeatStatus(int $seatId, string $status): void
    {
        if (! in_array($status, ['available', 'occupied', 'maintenance'], true)) {
            return;
        }

        $seat = Seat::whereHas('space.property', function ($query) {
            $query->where('user_id', auth()->id());
        })->find($seatId);

        if ($seat) {
            $seat->update(['status' => $status]);
        }
    }

    public function openEditModal(int $seatId): void
    {
        $seat = Seat::whereHas('space.property', function ($query) {
            $query->where('user_id', auth()->id());
        })->find($seatId);

        if (! $seat) {
            return;
        }

        $this->editingSeatId = $seat->id;
        $this->editSeatNumber = $seat->seat_number;
        $this->editSeatStatus = $seat->status;
        $this->editSeatPrice = $seat->price ? (string) $seat->price : null;
        $this->editSeatNotes = $seat->notes;
        $this->showEditModal = true;
    }

    public function closeEditModal(): void
    {
        $this->showEditModal = false;
        $this->editingSeatId = null;
        $this->resetValidation();
    }

    public function saveSeat(): void
    {
        $this->validate([
            'editSeatNumber' => 'required|string|max:50',
            'editSeatStatus' => 'required|in:available,occupied,maintenance',
            'editSeatPrice' => 'nullable|numeric|min:0',
            'editSeatNotes' => 'nullable|string|max:500',
        ]);

        if (! $this->editingSeatId) {
            return;
        }

        $seat = Seat::whereHas('space.property', function ($query) {
            $query->where('user_id', auth()->id());
        })->find($this->editingSeatId);

        if ($seat) {
            $seat->update([
                'seat_number' => $this->editSeatNumber,
                'status' => $this->editSeatStatus,
                'price' => $this->editSeatPrice ? (float) $this->editSeatPrice : null,
                'notes' => $this->editSeatNotes ?: null,
            ]);
        }

        $this->closeEditModal();
    }

    public function render(): View
    {
        $userId = auth()->id();

        $properties = Property::where('user_id', $userId)
            ->select('id', 'name', 'category')
            ->orderBy('name')
            ->get();

        $query = Seat::whereHas('space.property', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })->with(['space.property', 'seatRow']);

        if ($this->selectedPropertyId) {
            $query->whereHas('space', function ($q) {
                $q->where('property_id', $this->selectedPropertyId);
            });
        }

        if ($this->statusFilter === 'walkway') {
            $query->where('is_walkway', true);
        } elseif ($this->statusFilter !== 'all') {
            $query->where('is_walkway', false)
                ->where('status', $this->statusFilter);
        }

        if (trim($this->search) !== '') {
            $searchTerm = '%'.trim($this->search).'%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('seat_number', 'like', $searchTerm)
                    ->orWhereHas('seatRow', function ($rq) use ($searchTerm) {
                        $rq->where('row_label', 'like', $searchTerm)
                            ->orWhere('section_name', 'like', $searchTerm);
                    })
                    ->orWhereHas('space', function ($sq) use ($searchTerm) {
                        $sq->where('name', 'like', $searchTerm);
                    });
            });
        }

        // Summary counts
        $baseSeatQuery = Seat::whereHas('space.property', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        });

        if ($this->selectedPropertyId) {
            $baseSeatQuery->whereHas('space', function ($q) {
                $q->where('property_id', $this->selectedPropertyId);
            });
        }

        $allSeatsCount = (clone $baseSeatQuery)->count();
        $realSeatsCount = (clone $baseSeatQuery)->where('is_walkway', false)->count();
        $availableCount = (clone $baseSeatQuery)->where('is_walkway', false)->where('status', 'available')->count();
        $occupiedCount = (clone $baseSeatQuery)->where('is_walkway', false)->where('status', 'occupied')->count();
        $walkwayCount = (clone $baseSeatQuery)->where('is_walkway', true)->count();

        $seats = $query->orderBy('space_id')
            ->orderBy('seat_row_id')
            ->orderBy('column_index')
            ->paginate(20);

        return view('livewire.vendor.seat-inventory', [
            'properties' => $properties,
            'seats' => $seats,
            'allSeatsCount' => $allSeatsCount,
            'realSeatsCount' => $realSeatsCount,
            'availableCount' => $availableCount,
            'occupiedCount' => $occupiedCount,
            'walkwayCount' => $walkwayCount,
        ]);
    }
}
