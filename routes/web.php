<?php

use App\Http\Controllers\LibraryApiController;
use App\Livewire\Admin\AdminManager;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\PropertyOverview;
use App\Livewire\Admin\UserManager;
use App\Livewire\Admin\VendorManager;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Customer\MyBookings;
use App\Livewire\Public\PropertyListing;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Rebuild API Endpoints
Route::get('/api/library/data', [LibraryApiController::class, 'getData'])->name('api.library.data');
Route::post('/api/library/sync', [LibraryApiController::class, 'handleSync'])->name('api.library.sync');

// Public Directory & Space Explorer Routes
Route::get('/', PropertyListing::class)->name('home');
Route::get('/space/{property}', fn ($property) => redirect()->route('home', ['space' => $property]))->name('space.detail');

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/register', Register::class)->name('register');
    Route::get('/login', Login::class)->name('login');
});

// Authenticated Customer Routes
Route::middleware('auth')->group(function () {
    Route::get('/my-bookings', MyBookings::class)->name('customer.bookings');
    Route::get('/student/dashboard', fn () => redirect()->route('customer.bookings'))->name('student.dashboard');
});

// Owner / Vendor Portal Routes (Requires authentication and vendor role)
Route::prefix('owner')->group(function () {
    Route::get('/', function () {
        if (! Auth::check()) {
            return redirect()->route('login', ['role' => 'owner']);
        }
        if (! Auth::user()->isVendor()) {
            return redirect()->route('customer.bookings');
        }

        return redirect('/#owner');
    })->name('owner.dashboard');

    Route::get('/dashboard', function () {
        if (! Auth::check()) {
            return redirect()->route('login', ['role' => 'owner']);
        }
        if (! Auth::user()->isVendor()) {
            return redirect()->route('customer.bookings');
        }

        return redirect('/#dash');
    })->name('vendor.dashboard');

    Route::get('/seats', function () {
        if (! Auth::check()) {
            return redirect()->route('login', ['role' => 'owner']);
        }
        if (! Auth::user()->isVendor()) {
            return redirect()->route('customer.bookings');
        }

        return redirect('/#seats');
    })->name('vendor.seats');

    Route::get('/students', function () {
        if (! Auth::check()) {
            return redirect()->route('login', ['role' => 'owner']);
        }
        if (! Auth::user()->isVendor()) {
            return redirect()->route('customer.bookings');
        }

        return redirect('/#students');
    })->name('vendor.students');

    Route::get('/fees', function () {
        if (! Auth::check()) {
            return redirect()->route('login', ['role' => 'owner']);
        }
        if (! Auth::user()->isVendor()) {
            return redirect()->route('customer.bookings');
        }

        return redirect('/#fees');
    })->name('vendor.fees');

    Route::get('/bookings', function () {
        if (! Auth::check()) {
            return redirect()->route('login', ['role' => 'owner']);
        }
        if (! Auth::user()->isVendor()) {
            return redirect()->route('customer.bookings');
        }

        return redirect('/#reqs');
    })->name('vendor.bookings');

    Route::get('/library', function () {
        if (! Auth::check()) {
            return redirect()->route('login', ['role' => 'owner']);
        }
        if (! Auth::user()->isVendor()) {
            return redirect()->route('customer.bookings');
        }

        return redirect('/#library');
    })->name('vendor.library');

    Route::get('/properties', fn () => redirect()->route('vendor.library'))->name('properties.index');
    Route::get('/properties/{property}/builder', fn () => redirect()->route('vendor.seats'))->name('spaces.builder');
    Route::get('/profile', fn () => redirect()->route('vendor.library'))->name('vendor.profile');
});

// Backward compatibility redirects for legacy root paths
Route::get('/dashboard', function () {
    if (! Auth::check()) {
        return redirect()->route('login');
    }
    if (Auth::user()->isVendor()) {
        return redirect('/#dash');
    }

    return redirect()->route('customer.bookings');
})->name('dashboard');

Route::get('/properties', function () {
    if (! Auth::check() || ! Auth::user()->isVendor()) {
        return redirect()->route('home');
    }

    return redirect('/#library');
});

Route::get('/seats', function () {
    if (! Auth::check() || ! Auth::user()->isVendor()) {
        return redirect()->route('home');
    }

    return redirect('/#seats');
});

Route::post('/logout', function () {
    Auth::logout();
    session()->invalidate();
    session()->regenerateToken();

    return redirect()->route('login');
})->name('logout');

// Super Admin & Admin Panel Routes (Prefix: /cwladminpnl)
Route::prefix('cwladminpnl')->name('admin.')->group(function () {
    // Admin Guest Login
    Route::get('/login', App\Livewire\Admin\Login::class)->name('login');

    // Switch Back to Admin Route
    Route::match(['get', 'post'], '/switch-back', function () {
        if (session()->has('impersonator_id')) {
            $adminId = session()->pull('impersonator_id');
            $admin = User::find($adminId);
            if ($admin && $admin->isAdministrator()) {
                Auth::login($admin);
                session()->regenerate();

                return redirect()->route('admin.users');
            }
        }

        return redirect()->route('admin.login');
    })->name('switch-back');

    // Authenticated Admin Routes
    Route::middleware(['admin'])->group(function () {
        Route::get('/', Dashboard::class)->name('dashboard');
        Route::get('/users', UserManager::class)->name('users');
        Route::get('/admins', AdminManager::class)->name('admins');
        Route::get('/vendors', VendorManager::class)->name('vendors');
        Route::get('/properties', PropertyOverview::class)->name('properties');

        Route::post('/logout', function () {
            Auth::logout();
            session()->invalidate();
            session()->regenerateToken();

            return redirect()->route('admin.login');
        })->name('logout');
    });
});
