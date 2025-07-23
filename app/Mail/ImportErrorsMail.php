<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Mailable class for sending import failure notifications with error details.
 *
 * @class ImportErrorsMail
 */
class ImportErrorsMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The import instance related to the failed import.
     *
     * @var mixed
     */
    public $import;

    /**
     * Array of rows that failed during the import process.
     *
     * @var array
     */
    public array $errorRows;

    /**
     * Create a new message instance.
     *
     * @param mixed $import
     * @param array $errorRows
     */
    public function __construct($import, array $errorRows)
    {
        $this->import = $import;
        $this->errorRows = $errorRows;
    }

    /**
     * Get the message envelope.
     *
     * @return Envelope
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Import Failed: ' . $this->import->file_name,
        );
    }

    /**
     * Get the message content definition.
     *
     * @return Content
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.import',
            with: [
                'import'    => $this->import,
                'errorRows' => $this->errorRows,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments(): array
    {
        return [];
    }
}
