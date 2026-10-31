<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>New Booking Request</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f8fafc; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #1e293b;">
    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #f8fafc; padding: 40px 20px;">
        <tr>
            <td align="center">
                <table width="100%" max-width="540" border="0" cellspacing="0" cellpadding="0" style="max-width: 540px; background-color: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
                    <!-- Brand Header -->
                    <tr>
                        <td align="center" style="background: linear-gradient(135deg, #1e1b4b 0%, #0f172a 100%); padding: 32px 24px 24px 24px;">
                            <div style="font-size: 22px; font-weight: 800; color: #ffffff; letter-spacing: -0.5px;">
                                Cowork<span style="color: #818cf8;">Library</span>
                            </div>
                            <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 1.5px; color: #34d399; margin-top: 6px; font-weight: 700;">
                                Property Owner Alert
                            </div>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td style="padding: 32px 28px;">
                            <div style="display: inline-block; padding: 4px 10px; background-color: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 9999px; font-size: 11px; font-weight: 700; color: #047857; margin-bottom: 12px;">
                                🔔 New Seat Booking Applied
                            </div>

                            <h2 style="margin: 0 0 12px 0; font-size: 20px; font-weight: 800; color: #0f172a;">
                                Someone wants to book a seat!
                            </h2>
                            <p style="margin: 0 0 24px 0; font-size: 14px; line-height: 1.6; color: #475569;">
                                Hello <strong>{{ $booking->property->user->name }}</strong>,<br>
                                A customer has just applied to book a seat at your property <strong>{{ $booking->property->name }}</strong>. Here are the booking details:
                            </p>

                            <!-- Booking Card -->
                            <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; margin-bottom: 24px;">
                                <table width="100%" border="0" cellspacing="0" cellpadding="6">
                                    <tr>
                                        <td width="40%" style="font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase;">Property</td>
                                        <td style="font-size: 14px; color: #0f172a; font-weight: 700;">{{ $booking->property->name }}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase;">Floor / Space</td>
                                        <td style="font-size: 14px; color: #0f172a; font-weight: 600;">{{ $booking->space->name }}</td>
                                    </tr>
                                    @if(! empty($allBookings) && count($allBookings) > 1)
                                    <tr>
                                        <td style="font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase;">Seats Requested</td>
                                        <td style="font-size: 16px; color: #4f46e5; font-weight: 800;">
                                            {{ collect($allBookings)->pluck('seat_number')->implode(', ') }} ({{ count($allBookings) }} Seats)
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase;">Total Monthly Price</td>
                                        <td style="font-size: 15px; color: #059669; font-weight: 700;">
                                            ₹{{ number_format((float) collect($allBookings)->sum('monthly_price'), 2) }}/mo
                                        </td>
                                    </tr>
                                    @else
                                    <tr>
                                        <td style="font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase;">Seat Number</td>
                                        <td style="font-size: 16px; color: #4f46e5; font-weight: 800;">{{ $booking->seat_number }}</td>
                                    </tr>
                                    @if($booking->monthly_price)
                                    <tr>
                                        <td style="font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase;">Monthly Price</td>
                                        <td style="font-size: 14px; color: #059669; font-weight: 700;">₹{{ number_format((float) $booking->monthly_price, 2) }}/mo</td>
                                    </tr>
                                    @endif
                                    @endif
                                    @if($booking->shift_timing)
                                    <tr>
                                        <td style="font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase;">Shift / Slot</td>
                                        <td style="font-size: 13px; color: #334155; font-weight: 600;">{{ $booking->shift_timing }}</td>
                                    </tr>
                                    @endif
                                    @if($booking->notes)
                                    <tr>
                                        <td style="font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase;">Customer Note</td>
                                        <td style="font-size: 13px; color: #334155;">{{ $booking->notes }}</td>
                                    </tr>
                                    @endif
                                </table>
                            </div>

                            <!-- Customer Profile Card -->
                            <div style="background-color: #f1f5f9; border-radius: 12px; padding: 16px 20px; margin-bottom: 24px;">
                                <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: #475569; margin-bottom: 8px;">
                                    Applicant Contact Information
                                </div>
                                <div style="font-size: 14px; font-weight: 700; color: #0f172a;">
                                    👤 {{ $booking->user->name }}
                                </div>
                                <div style="font-size: 13px; color: #475569; margin-top: 4px;">
                                    ✉️ {{ $booking->user->email }}
                                </div>
                                @if($booking->user->phone)
                                <div style="font-size: 13px; color: #475569; margin-top: 4px;">
                                    📞 Mobile: <strong>{{ $booking->user->phone }}</strong>
                                </div>
                                @endif
                                @if($booking->user->whatsapp_number)
                                <div style="font-size: 13px; color: #059669; margin-top: 4px;">
                                    💬 WhatsApp: <strong>{{ $booking->user->whatsapp_number }}</strong>
                                </div>
                                @endif
                            </div>

                            <p style="margin: 0 0 24px 0; font-size: 13px; color: #64748b; line-height: 1.5;">
                                When you approve this request in your Owner Portal, Seat <strong>{{ $booking->seat_number }}</strong> will be automatically reserved and marked as occupied.
                            </p>

                            <!-- CTA Button -->
                            <div style="text-align: center; margin: 32px 0 16px 0;">
                                <a href="{{ route('vendor.bookings') }}" style="display: inline-block; padding: 14px 28px; background-color: #4f46e5; color: #ffffff; text-decoration: none; border-radius: 10px; font-size: 14px; font-weight: 700; box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.4);">
                                    Review &amp; Approve in Owner Portal &rarr;
                                </a>
                            </div>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td align="center" style="background-color: #f8fafc; padding: 18px 24px; border-top: 1px solid #f1f5f9; font-size: 11px; color: #94a3b8;">
                            &copy; {{ date('Y') }} CoworkLibrary &bull; Real-time Space &amp; Cinema-Style Seat Grid Engine.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
