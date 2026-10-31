<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Booking Confirmed!</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f8fafc; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #1e293b;">
    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #f8fafc; padding: 40px 20px;">
        <tr>
            <td align="center">
                <table width="100%" max-width="540" border="0" cellspacing="0" cellpadding="0" style="max-width: 540px; background-color: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
                    <!-- Brand Header -->
                    <tr>
                        <td align="center" style="background: linear-gradient(135deg, #064e3b 0%, #065f46 100%); padding: 32px 24px 24px 24px;">
                            <div style="font-size: 22px; font-weight: 800; color: #ffffff; letter-spacing: -0.5px;">
                                Cowork<span style="color: #6ee7b7;">Library</span>
                            </div>
                            <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 1.5px; color: #a7f3d0; margin-top: 6px; font-weight: 700;">
                                Seat Booking Confirmed
                            </div>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td style="padding: 32px 28px;">
                            <div style="display: inline-block; padding: 4px 10px; background-color: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 9999px; font-size: 11px; font-weight: 700; color: #047857; margin-bottom: 12px;">
                                ✅ Request Approved
                            </div>

                            <h2 style="margin: 0 0 12px 0; font-size: 20px; font-weight: 800; color: #0f172a;">
                                Congratulations, {{ $booking->user->name }}!
                            </h2>
                            <p style="margin: 0 0 24px 0; font-size: 14px; line-height: 1.6; color: #475569;">
                                Great news! The owner of <strong>{{ $booking->property->name }}</strong> has approved your seat booking request. Your seat is now officially reserved for you.
                            </p>

                            <!-- Booking Card -->
                            <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; margin-bottom: 24px;">
                                <table width="100%" border="0" cellspacing="0" cellpadding="6">
                                    <tr>
                                        <td width="40%" style="font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase;">Property</td>
                                        <td style="font-size: 14px; color: #0f172a; font-weight: 700;">{{ $booking->property->name }}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase;">Address</td>
                                        <td style="font-size: 13px; color: #334155;">{{ $booking->property->address }}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase;">Floor / Space</td>
                                        <td style="font-size: 14px; color: #0f172a; font-weight: 600;">{{ $booking->space->name }}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase;">Confirmed Seat</td>
                                        <td style="font-size: 18px; color: #059669; font-weight: 800;">{{ $booking->seat_number }}</td>
                                    </tr>
                                    @if($booking->monthly_price)
                                    <tr>
                                        <td style="font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase;">Monthly Price</td>
                                        <td style="font-size: 14px; color: #0f172a; font-weight: 700;">₹{{ number_format((float) $booking->monthly_price, 2) }}/mo</td>
                                    </tr>
                                    @endif
                                    @if($booking->shift_timing)
                                    <tr>
                                        <td style="font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase;">Shift / Slot</td>
                                        <td style="font-size: 13px; color: #334155; font-weight: 600;">{{ $booking->shift_timing }}</td>
                                    </tr>
                                    @endif
                                </table>
                            </div>

                            <!-- Property Owner Contact -->
                            <div style="background-color: #f1f5f9; border-radius: 12px; padding: 16px 20px; margin-bottom: 24px;">
                                <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: #475569; margin-bottom: 8px;">
                                    Space Contact
                                </div>
                                <div style="font-size: 14px; font-weight: 700; color: #0f172a;">
                                    🏢 {{ $booking->property->user->name }}
                                </div>
                                <div style="font-size: 13px; color: #475569; margin-top: 4px;">
                                    ✉️ {{ $booking->property->user->email }}
                                </div>
                                @if($booking->property->user->phone)
                                <div style="font-size: 13px; color: #475569; margin-top: 4px;">
                                    📞 Mobile: <strong>{{ $booking->property->user->phone }}</strong>
                                </div>
                                @endif
                                @if($booking->property->user->whatsapp_number)
                                <div style="font-size: 13px; color: #059669; margin-top: 4px;">
                                    💬 WhatsApp: <strong>{{ $booking->property->user->whatsapp_number }}</strong>
                                </div>
                                @endif
                            </div>

                            <!-- CTA Button -->
                            <div style="text-align: center; margin: 32px 0 16px 0;">
                                <a href="{{ route('customer.bookings') }}" style="display: inline-block; padding: 14px 28px; background-color: #059669; color: #ffffff; text-decoration: none; border-radius: 10px; font-size: 14px; font-weight: 700; box-shadow: 0 4px 6px -1px rgba(5, 150, 105, 0.4);">
                                    View My Bookings &rarr;
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
