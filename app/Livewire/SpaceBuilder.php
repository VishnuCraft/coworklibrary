<?php

namespace App\Livewire;

use App\Models\Property;
use App\Models\Seat;
use App\Models\SeatRow;
use App\Models\Space;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.vendor')]
class SpaceBuilder extends Component
{
    public Property $property;

    public ?int $selectedFloorId = null;

    // Floor Modal / Inline Add
    public string $newFloorName = '';

    public string $newFloorType = 'floor'; // floor, deck, zone, building

    public bool $showAddFloorModal = false;

    // Row & Seat Generator state
    public string $sectionName = 'Hall'; // e.g. "Hall", "Kitchen", "Cabin", "Discussion Room"

    public string $newRowLabel = 'Row 1';

    public int $newRowSeatsCount = 10;

    public string $newRowSeatPrefix = 'R1-';

    public string $newRowType = 'seat'; // seat, desk, bed, cabin

    // Bulk Generator state
    public bool $showBulkModal = false;

    public string $bulkSectionName = 'Hall';

    public string $bulkStartLetter = 'A';

    public string $bulkEndLetter = 'E';

    public int $bulkSeatsPerRow = 8;

    public string $bulkSeatType = 'seat';

    // Seat Quick Edit Modal state
    public ?int $editingSeatId = null;

    public string $editSeatNumber = '';

    public string $editSeatStatus = 'available';

    public string $editSeatType = 'seat';

    public string $editSeatPrice = '';

    public string $editSeatNotes = '';

    public function mount(Property $property): void
    {
        $this->property = $property;

        if ($this->property->user_id !== null && $this->property->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this property.');
        }

        // Auto-select first floor if available
        $firstFloor = $this->property->topLevelSpaces()->first();
        if ($firstFloor) {
            $this->selectedFloorId = $firstFloor->id;
        }
    }

    public function selectFloor(int $floorId): void
    {
        $this->selectedFloorId = $floorId;
    }

    public function addFloor(): void
    {
        $this->validate([
            'newFloorName' => 'required|min:1|max:100',
            'newFloorType' => 'required|string',
        ]);

        $maxSort = Space::where('property_id', $this->property->id)
            ->whereNull('parent_id')
            ->max('sort_order') ?? 0;

        $floor = Space::create([
            'property_id' => $this->property->id,
            'parent_id' => null,
            'name' => $this->newFloorName,
            'type' => $this->newFloorType,
            'sort_order' => $maxSort + 1,
        ]);

        $this->newFloorName = '';
        $this->showAddFloorModal = false;
        $this->selectedFloorId = $floor->id;
    }

    public function deleteFloor(int $floorId): void
    {
        $floor = Space::where('property_id', $this->property->id)->find($floorId);
        if ($floor) {
            $floor->delete();
            if ($this->selectedFloorId === $floorId) {
                $first = $this->property->topLevelSpaces()->first();
                $this->selectedFloorId = $first?->id;
            }
        }
    }

    public function addRowWithSeats(): void
    {
        $this->validate([
            'selectedFloorId' => 'required|exists:spaces,id',
            'sectionName' => 'required|string|max:100',
            'newRowLabel' => 'required|string|max:50',
            'newRowSeatsCount' => 'required|integer|min:1|max:50',
            'newRowSeatPrefix' => 'nullable|string|max:20',
            'newRowType' => 'required|in:seat,desk,bed,cabin',
        ]);

        $maxOrder = SeatRow::where('space_id', $this->selectedFloorId)->max('row_order') ?? 0;

        $row = SeatRow::create([
            'space_id' => $this->selectedFloorId,
            'section_name' => trim($this->sectionName) ?: 'Main Area',
            'row_label' => $this->newRowLabel,
            'row_order' => $maxOrder + 1,
            'total_columns' => $this->newRowSeatsCount,
        ]);

        $prefix = $this->newRowSeatPrefix ?: $this->newRowLabel.'-';

        for ($col = 1; $col <= $this->newRowSeatsCount; $col++) {
            Seat::create([
                'space_id' => $this->selectedFloorId,
                'seat_row_id' => $row->id,
                'seat_number' => $prefix.$col,
                'column_index' => $col,
                'type' => $this->newRowType,
                'is_walkway' => false,
                'status' => 'available',
            ]);
        }

        // Increment for next quick add
        $this->newRowLabel = 'Row '.($maxOrder + 2);
        $this->newRowSeatPrefix = 'R'.($maxOrder + 2).'-';
    }

    public function bulkGenerateRows(): void
    {
        $this->validate([
            'selectedFloorId' => 'required|exists:spaces,id',
            'bulkSectionName' => 'required|string|max:100',
            'bulkStartLetter' => 'required|string|max:2',
            'bulkEndLetter' => 'required|string|max:2',
            'bulkSeatsPerRow' => 'required|integer|min:1|max:50',
            'bulkSeatType' => 'required|in:seat,desk,bed,cabin',
        ]);

        $startChar = strtoupper(trim($this->bulkStartLetter))[0];
        $endChar = strtoupper(trim($this->bulkEndLetter))[0];

        if (ord($startChar) > ord($endChar)) {
            $temp = $startChar;
            $startChar = $endChar;
            $endChar = $temp;
        }

        $currentMaxOrder = SeatRow::where('space_id', $this->selectedFloorId)->max('row_order') ?? 0;

        for ($char = ord($startChar); $char <= ord($endChar); $char++) {
            $letter = chr($char);
            $currentMaxOrder++;

            $row = SeatRow::create([
                'space_id' => $this->selectedFloorId,
                'section_name' => trim($this->bulkSectionName) ?: 'Main Area',
                'row_label' => 'Row '.$letter,
                'row_order' => $currentMaxOrder,
                'total_columns' => $this->bulkSeatsPerRow,
            ]);

            for ($col = 1; $col <= $this->bulkSeatsPerRow; $col++) {
                Seat::create([
                    'space_id' => $this->selectedFloorId,
                    'seat_row_id' => $row->id,
                    'seat_number' => $letter.$col,
                    'column_index' => $col,
                    'type' => $this->bulkSeatType,
                    'is_walkway' => false,
                    'status' => 'available',
                ]);
            }
        }

        $this->showBulkModal = false;
    }

    /**
     * Cinema Aisle feature: Toggle whether this slot is a seat or an empty walking aisle!
     */
    public function toggleWalkway(int $seatId): void
    {
        $seat = Seat::find($seatId);
        if ($seat) {
            $seat->is_walkway = ! $seat->is_walkway;
            $seat->save();
        }
    }

    public function addSeatToRow(int $seatRowId): void
    {
        $row = SeatRow::find($seatRowId);
        if (! $row) {
            return;
        }

        $maxCol = $row->seats()->max('column_index') ?? 0;
        $newCol = $maxCol + 1;

        Seat::create([
            'space_id' => $row->space_id,
            'seat_row_id' => $row->id,
            'seat_number' => $row->row_label.'-'.$newCol,
            'column_index' => $newCol,
            'type' => 'seat',
            'is_walkway' => false,
            'status' => 'available',
        ]);

        $row->total_columns = $newCol;
        $row->save();
    }

    public function removeLastSeatFromRow(int $seatRowId): void
    {
        $row = SeatRow::find($seatRowId);
        if (! $row) {
            return;
        }

        $lastSeat = $row->seats()->orderByDesc('column_index')->first();
        if ($lastSeat) {
            $lastSeat->delete();
            $row->total_columns = max(0, $row->seats()->count());
            $row->save();
        }
    }

    public function deleteRow(int $seatRowId): void
    {
        $row = SeatRow::find($seatRowId);
        if ($row) {
            $row->delete();
        }
    }

    /**
     * Reorder rows based on drag-and-drop order.
     *
     * @param  array<int|string>  $orderedRowIds
     */
    public function reorderRows(array $orderedRowIds): void
    {
        if (empty($orderedRowIds) || ! $this->selectedFloorId) {
            return;
        }

        $rowIds = array_map('intval', $orderedRowIds);

        // Fetch rows to know their existing order positions
        $existingRows = SeatRow::whereIn('id', $rowIds)
            ->where('space_id', $this->selectedFloorId)
            ->orderBy('row_order')
            ->get();

        if ($existingRows->isEmpty()) {
            return;
        }

        // Get sorted list of row_order values currently allocated to these rows
        $orderSlots = $existingRows->pluck('row_order')->sort()->values();

        foreach ($rowIds as $index => $rowId) {
            if (isset($orderSlots[$index])) {
                SeatRow::where('id', $rowId)
                    ->where('space_id', $this->selectedFloorId)
                    ->update(['row_order' => $orderSlots[$index]]);
            }
        }
    }

    /**
     * Move a single row up by one position within its section.
     */
    public function moveRowUp(int $seatRowId): void
    {
        $currentRow = SeatRow::where('id', $seatRowId)
            ->where('space_id', $this->selectedFloorId)
            ->first();

        if (! $currentRow) {
            return;
        }

        $prevRow = SeatRow::where('space_id', $this->selectedFloorId)
            ->where('section_name', $currentRow->section_name)
            ->where('row_order', '<', $currentRow->row_order)
            ->orderByDesc('row_order')
            ->first();

        if ($prevRow) {
            $currentOrder = $currentRow->row_order;
            $prevOrder = $prevRow->row_order;

            if ($currentOrder === $prevOrder) {
                $prevOrder = max(1, $currentOrder - 1);
            }

            $currentRow->update(['row_order' => $prevOrder]);
            $prevRow->update(['row_order' => $currentOrder]);
        }
    }

    /**
     * Move a single row down by one position within its section.
     */
    public function moveRowDown(int $seatRowId): void
    {
        $currentRow = SeatRow::where('id', $seatRowId)
            ->where('space_id', $this->selectedFloorId)
            ->first();

        if (! $currentRow) {
            return;
        }

        $nextRow = SeatRow::where('space_id', $this->selectedFloorId)
            ->where('section_name', $currentRow->section_name)
            ->where('row_order', '>', $currentRow->row_order)
            ->orderBy('row_order')
            ->first();

        if ($nextRow) {
            $currentOrder = $currentRow->row_order;
            $nextOrder = $nextRow->row_order;

            if ($currentOrder === $nextOrder) {
                $nextOrder = $currentOrder + 1;
            }

            $currentRow->update(['row_order' => $nextOrder]);
            $nextRow->update(['row_order' => $currentOrder]);
        }
    }

    public function editSeat(int $seatId): void
    {
        $seat = Seat::find($seatId);
        if (! $seat) {
            return;
        }

        $this->editingSeatId = $seat->id;
        $this->editSeatNumber = $seat->seat_number;
        $this->editSeatStatus = $seat->status;
        $this->editSeatType = $seat->type;
        $this->editSeatPrice = (string) ($seat->price ?? '');
        $this->editSeatNotes = $seat->notes ?? '';
    }

    public function closeSeatEdit(): void
    {
        $this->editingSeatId = null;
    }

    public function saveSeat(): void
    {
        if (! $this->editingSeatId) {
            return;
        }

        $seat = Seat::find($this->editingSeatId);
        if ($seat) {
            $seat->update([
                'seat_number' => $this->editSeatNumber,
                'status' => $this->editSeatStatus,
                'type' => $this->editSeatType,
                'price' => $this->editSeatPrice !== '' ? (float) $this->editSeatPrice : null,
                'notes' => $this->editSeatNotes ?: null,
            ]);
        }

        $this->editingSeatId = null;
    }

    public function render(): View
    {
        $floors = $this->property->topLevelSpaces()->get();
        $currentFloor = $floors->firstWhere('id', $this->selectedFloorId);

        $seatRows = $currentFloor
            ? $currentFloor->seatRows()->with('seats')->orderBy('row_order')->get()
            : collect();

        // Group rows by their section name (e.g. "Hall", "Kitchen", "Cabin")
        $groupedRows = $seatRows->groupBy('section_name');

        $totalSeats = $seatRows->flatMap->seats->where('is_walkway', false)->count();
        $totalWalkways = $seatRows->flatMap->seats->where('is_walkway', true)->count();
        $availableSeats = $seatRows->flatMap->seats->where('is_walkway', false)->where('status', 'available')->count();
        $occupiedSeats = $seatRows->flatMap->seats->where('is_walkway', false)->where('status', 'occupied')->count();

        return view('livewire.space-builder', [
            'floors' => $floors,
            'currentFloor' => $currentFloor,
            'seatRows' => $seatRows,
            'groupedRows' => $groupedRows,
            'totalSeats' => $totalSeats,
            'totalWalkways' => $totalWalkways,
            'availableSeats' => $availableSeats,
            'occupiedSeats' => $occupiedSeats,
        ]);
    }
}
