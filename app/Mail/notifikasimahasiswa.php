<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class NotifikasiMahasiswa extends Mailable
{
    public $nama;
    public $subjectText;
    public $messageText;
    public $attachmentData;
    public $attachmentName;
    public $attachmentMime;

    public function __construct(
        $nama,
        $subjectText = 'Notifikasi SIAKAD',
        $messageText = null,
        $attachmentData = null,
        $attachmentName = null,
        $attachmentMime = null
    ) {
        $this->nama           = $nama;
        $this->subjectText    = $subjectText;
        $this->messageText    = $messageText;
        $this->attachmentData = $attachmentData;
        $this->attachmentName = $attachmentName;
        $this->attachmentMime = $attachmentMime ?: $this->guessMime($attachmentName);
    }

    public function build()
    {
        $mail = $this->subject($this->subjectText)
            ->view('notifikasi')
            ->with([
                'nama'    => $this->nama,
                'subject' => $this->subjectText,
                'body'    => $this->messageText,
            ]);

        if ($this->attachmentData && $this->attachmentName) {
            $mail->attachData($this->attachmentData, $this->attachmentName, [
                'mime' => $this->attachmentMime,
            ]);
        }

        return $mail;
    }

    private function guessMime(?string $filename): string
    {
        $ext = strtolower(pathinfo($filename ?? '', PATHINFO_EXTENSION));

        $map = [
            'pdf'  => 'application/pdf',
            'doc'  => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls'  => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'ppt'  => 'application/vnd.ms-powerpoint',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png'  => 'image/png',
            'gif'  => 'image/gif',
            'zip'  => 'application/zip',
            'rar'  => 'application/x-rar-compressed',
            'txt'  => 'text/plain',
        ];

        return $map[$ext] ?? 'application/octet-stream';
    }
}
