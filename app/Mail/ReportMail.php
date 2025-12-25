<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $fileName;
    public $filePath;
    public $format;
    public $userName;
    public $userRole;

    /**
     * Create a new message instance.
     */
    public function __construct($fileName, $filePath, $format, $userName, $userRole)
    {
        $this->fileName = $fileName;
        $this->filePath = $filePath;
        $this->format = $format;
        $this->userName = $userName;
        $this->userRole = $userRole;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $message = $this->subject('Sales Report - ' . $this->userRole . ' (' . $this->userName . ')')
                    ->view('emails.report', [
                        'userName' => $this->userName,
                        'userRole' => $this->userRole,
                        'format' => strtoupper($this->format),
                    ]);
        
        // Attach file if it exists
        if (file_exists($this->filePath)) {
            $message->attach($this->filePath, [
                'as' => $this->fileName,
                'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
        }
        
        return $message;
    }
}

