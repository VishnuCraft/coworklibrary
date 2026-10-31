<?php

namespace App\Livewire\Admin;

use App\Models\Property;
use App\Models\Seat;
use App\Models\Space;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class Dashboard extends Component
{
    public function render(): View
    {
        $totalVendors = User::where('role', 'vendor')->count();
        $totalCustomers = User::where('role', 'customer')->count();
        $totalAdmins = User::whereIn('role', ['administrator', 'super_admin', 'admin'])->count();
        $totalProperties = Property::count();
        $totalFloors = Space::whereNull('parent_id')->count();

        $totalSeats = Seat::where('is_walkway', false)->count();
        $totalWalkways = Seat::where('is_walkway', true)->count();
        $availableSeats = Seat::where('is_walkway', false)->where('status', 'available')->count();
        $occupiedSeats = Seat::where('is_walkway', false)->where('status', 'occupied')->count();

        $recentVendors = User::where('role', 'vendor')
            ->withCount('properties')
            ->latest()
            ->take(5)
            ->get();

        $recentProperties = Property::with(['user', 'spaces.seats'])
            ->withCount('topLevelSpaces')
            ->latest()
            ->take(5)
            ->get();

        return view('livewire.admin.dashboard', [
            'totalVendors' => $totalVendors,
            'totalCustomers' => $totalCustomers,
            'totalAdmins' => $totalAdmins,
            'totalProperties' => $totalProperties,
            'totalFloors' => $totalFloors,
            'totalSeats' => $totalSeats,
            'totalWalkways' => $totalWalkways,
            'availableSeats' => $availableSeats,
            'occupiedSeats' => $occupiedSeats,
            'recentVendors' => $recentVendors,
            'recentProperties' => $recentProperties,
        ]);
    }
}
