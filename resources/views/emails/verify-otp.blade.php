<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Your Verification Code</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f8fafc; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #1e293b;">
    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #f8fafc; padding: 40px 20px;">
        <tr>
            <td align="center">
                <table width="100%" max-width="500" border="0" cellspacing="0" cellpadding="0" style="max-width: 500px; background-color: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
                    <!-- Brand Header -->
                    <tr>
                        <td align="center" style="background-color: #ffffff; padding: 32px 24px 20px 24px; border-bottom: 1px solid #f1f5f9;">
                            <div style="font-size: 22px; font-weight: 800; color: #0f172a; letter-spacing: -0.5px;">
                                Cowork<span style="color: #4f46e5;">Library</span>
                            </div>
                            <div style="font-size: 12px; color: #64748b; margin-top: 4px; font-weight: 500;">
                                Space & Cinema-Style Seat Management
                            </div>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td style="padding: 32px 28px;">
                            <h2 style="margin: 0 0 12px 0; font-size: 18px; font-weight: 700; color: #0f172a;">
                                Verify Your Email Address
                            </h2>
                            <p style="margin: 0 0 24px 0; font-size: 14px; line-height: 1.6; color: #475569;">
                                Hello <strong>{{ $name }}</strong>,<br>
                                Thank you for registering with CoworkLibrary. Please use the following 6-digit One-Time Password (OTP) to complete your account registration:
                            </p>

                            <!-- OTP Box -->
                            <div style="text-align: center; margin: 30px 0; padding: 20px; background-color: #f8fafc; border: 2px dashed #cbd5e1; border-radius: 12px;">
                                <span style="font-family: 'Courier New', Courier, monospace; font-size: 34px; font-weight: 800; letter-spacing: 8px; color: #4f46e5; display: inline-block;">
                                    {{ $otp }}
                                </span>
                            </div>

                            <p style="margin: 0 0 8px 0; font-size: 13px; color: #64748b; text-align: center;">
                                ⏱️ This code will expire in <strong>10 minutes</strong>.
                            </p>
                            <p style="margin: 0; font-size: 12px; color: #94a3b8; text-align: center;">
                                If you did not create an account on CoworkLibrary, you can safely ignore this email.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td align="center" style="background-color: #f8fafc; padding: 18px 24px; border-top: 1px solid #f1f5f9; font-size: 11px; color: #94a3b8;">
                            &copy; {{ date('Y') }} CoworkLibrary &bull; All rights reserved.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
