<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RecruitmentNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $applicantName,
        public string $subjectText,
        public string $bodyHtml
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subjectText,
        );
    }

    public function content(): Content
    {
        return new Content(
            htmlString: "
                <div style=\"font-family: 'Plus Jakarta Sans', sans-serif; color: #1E293B; line-height: 1.6; max-width: 600px; margin: 0 auto; border: 1px solid #E2E8F0; border-radius: 16px; padding: 24px;\">
                    <h3 style=\"color: #0F172A; border-bottom: 1px solid #E2E8F0; padding-bottom: 12px; margin-top: 0;\">SUPA / OUT University Careers</h3>
                    <p>Dear {$this->applicantName},</p>
                    <div>{$this->bodyHtml}</div>
                    <p style=\"border-top: 1px solid #E2E8F0; padding-top: 12px; margin-bottom: 0; font-size: 12px; color: #64748B;\">
                        This is an automated notification from the SUPA / OUT Recruitment System. Please do not reply directly to this email.
                    </p>
                </div>
            ",
        );
    }
}
