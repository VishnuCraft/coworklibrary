<?php

namespace App\Livewire\Auth;

use App\Mail\VerifyEmailOtpMail;
use App\Models\EmailOtp;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class Register extends Component
{
    public int $step = 1;

    public string $role = 'customer'; // customer or vendor

    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public bool $is_whatsapp_same = true;

    public string $whatsapp_number = '';

    public string $password = '';

    public string $password_confirmation = '';

    public string $otp = '';

    public ?string $statusMessage = null;

    public function mount(): void
    {
        $roleQuery = request()->query('role');
        if (in_array($roleQuery, ['vendor', 'owner'], true)) {
            $this->role = 'vendor';
        }
    }

    public function sendOtp(): void
    {
        $rules = [
            'role' => 'required|in:customer,vendor',
            'name' => 'required|string|min:2|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone' => 'required|string|min:10|max:20',
            'password' => 'required|string|min:6|confirmed',
        ];

        if (! $this->is_whatsapp_same) {
            $rules['whatsapp_number'] = 'required|string|min:10|max:20';
        }

        $this->validate($rules);

        $cleanEmail = strtolower(trim($this->email));
        $otpCode = (string) random_int(100000, 999999);
        $finalWhatsapp = $this->is_whatsapp_same ? trim($this->phone) : trim($this->whatsapp_number);

        // Store OTP and pending registration data in database
        EmailOtp::updateOrCreate(
            ['email' => $cleanEmail],
            [
                'otp' => $otpCode,
                'name' => $this->name,
                'phone' => trim($this->phone),
                'whatsapp_number' => $finalWhatsapp,
                'password' => Hash::make($this->password),
                'role' => $this->role,
                'expires_at' => now()->addMinutes(10),
            ]
        );

        // Send Email via configured SMTP
        try {
            Mail::to($cleanEmail)->send(new VerifyEmailOtpMail($otpCode, $this->name));
        } catch (\Throwable $e) {
            Log::warning('SMTP email send error: '.$e->getMessage());
        }

        $this->statusMessage = "A 6-digit verification code has been sent to {$cleanEmail}.";
        $this->step = 2;
    }

    public function verifyOtp(): void
    {
        $this->validate([
            'otp' => 'required|digits:6',
        ], [
            'otp.required' => 'Please enter the 6-digit OTP code.',
            'otp.digits' => 'The verification code must be 6 digits.',
        ]);

        $cleanEmail = strtolower(trim($this->email));

        $record = EmailOtp::where('email', $cleanEmail)
            ->where('otp', trim($this->otp))
            ->where('expires_at', '>', now())
            ->first();

        if (! $record) {
            $this->addError('otp', 'Invalid or expired OTP. Please check your email or click Resend OTP.');

            return;
        }

        // Create verified user with appropriate role (vendor / customer)
        $user = User::create([
            'name' => $record->name,
            'email' => $record->email,
            'phone' => $record->phone,
            'whatsapp_number' => $record->whatsapp_number,
            'password' => $record->password,
            'role' => $record->role ?? 'customer',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Clean up OTP from database
        $record->delete();

        Auth::login($user);
        session()->regenerate();

        if ($user->isVendor()) {
            $this->redirectRoute('owner.dashboard', navigate: true);
        } else {
            $this->redirectRoute('customer.bookings', navigate: true);
        }
    }

    public function resendOtp(): void
    {
        $cleanEmail = strtolower(trim($this->email));
        $record = EmailOtp::where('email', $cleanEmail)->first();

        if (! $record) {
            $this->step = 1;

            return;
        }

        $newOtp = (string) random_int(100000, 999999);
        $record->update([
            'otp' => $newOtp,
            'expires_at' => now()->addMinutes(10),
        ]);

        try {
            Mail::to($cleanEmail)->send(new VerifyEmailOtpMail($newOtp, $record->name));
        } catch (\Throwable $e) {
            Log::warning('SMTP email resend error: '.$e->getMessage());
        }

        $this->otp = '';
        $this->resetValidation();
        $this->statusMessage = "A fresh 6-digit verification code has been sent to {$cleanEmail}.";
    }

    public function backToStep1(): void
    {
        $this->step = 1;
        $this->otp = '';
        $this->resetValidation();
    }

    public function render(): View
    {
        return view('livewire.auth.register');
    }
}
