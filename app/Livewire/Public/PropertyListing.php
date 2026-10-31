<?php

namespace App\Livewire\Public;

use App\Http\Controllers\LibraryApiController;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.coworklibrary')]
#[Title('CoworkLibrary · Study Hall Desks & Fee Manager')]
class PropertyListing extends Component
{
    public function render(): View
    {
        $payload = app(LibraryApiController::class)->buildFullPayload();

        $currentUser = Auth::check() ? [
            'id' => Auth::id(),
            'name' => Auth::user()->name,
            'email' => Auth::user()->email,
            'phone' => Auth::user()->phone ?? '',
            'role' => Auth::user()->role,
            'is_owner' => Auth::user()->isVendor(),
        ] : null;

        return view('livewire.public.property-listing', [
            'initialData' => $payload,
            'currentUser' => $currentUser,
        ]);
    }
}
