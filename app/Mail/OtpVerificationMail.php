<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OtpVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $name,
        public string $otpCode
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('ictsupport@supa.ac.tz', 'SUPA / OUT ICT Support'),
            subject: 'SUPA Account OTP Verification Code',
        );
    }

    public function content(): Content
    {
        return new Content(
            htmlString: "
                <div style=\"font-family: Arial, sans-serif; color: #334155; line-height: 1.6; max-width: 600px; margin: 0 auto; border: 1px solid #e2e8f0; border-radius: 16px; padding: 32px; background-color: #ffffff;\">
                    <div style=\"text-align: center; border-bottom: 2px solid #3b82f6; padding-bottom: 16px; margin-bottom: 24px;\">
                        <h2 style=\"color: #1e3a8a; margin: 0;\">SUPA / OUT University</h2>
                        <p style=\"font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: #64748b; margin: 4px 0 0 0;\">Admission Management System</p>
                    </div>
                    <p style=\"font-size: 14px; font-weight: bold;\">Hello {$this->name},</p>
                    <p style=\"font-size: 13px; color: #475569;\">Thank you for registering on our portal. To complete your account creation and proceed with your application, please use the following One-Time Password (OTP) verification code:</p>
                    
                    <div style=\"text-align: center; margin: 32px 0;\">
                        <span style=\"font-family: monospace; font-size: 32px; font-weight: 900; letter-spacing: 6px; background-color: #f1f5f9; color: #1e3a8a; padding: 12px 24px; border-radius: 12px; border: 1px solid #cbd5e1; display: inline-block;\">
                            {$this->otpCode}
                        </span>
                    </div>

                    <p style=\"font-size: 12px; color: #64748b;\">This code is valid for 15 minutes. If you did not initiate this registration, please contact our support team immediately.</p>
                    
                    <div style=\"border-top: 1px solid #e2e8f0; padding-top: 20px; margin-top: 32px; font-size: 11px; color: #94a3b8; text-align: center;\">
                        This is an automated security notification from SUPA/OUT ICT Support. Please do not reply directly to this email.<br>
                        Contact: <strong>ictsupport@supa.ac.tz</strong>
                    </div>
                </div>
            ",
        );
    }
}
