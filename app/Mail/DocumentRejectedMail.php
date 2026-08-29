<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DocumentRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $applicantName,
        public string $docName,
        public string $comment
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Document Rejected - Action Required',
        );
    }

    public function content(): Content
    {
        return new Content(
            htmlString: "
                <h3>Dear {$this->applicantName},</h3>
                <p>We are reviewing your application and noticed that your uploaded document <strong>" . strtoupper($this->docName) . "</strong> has been rejected.</p>
                <p><strong>Rejection Reason:</strong> {$this->comment}</p>
                <p>Please log in to your portal and re-upload the correct document in Step 5 of the application.</p>
                <p>Thank you,<br>STTC SUPA Admission Desk</p>
            ",
        );
    }
}
