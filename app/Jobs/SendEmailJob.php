<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $emailData;

    public function __construct($emailData)
    {
        $this->emailData = $emailData;
    }

    public function handle(): void
    {
        $emails = explode(',', $this->emailData['to']);
        $subject = $this->emailData['subject'];
        $description = $this->emailData['description'];
        $attachments = $this->emailData['attachments'] ?? [];

        foreach ($emails as $email) {
            Mail::send([], [], function ($message) use ($email, $subject, $description, $attachments) {
                $message->to(trim($email))
                        ->subject($subject)
                        ->html($description);

                foreach ($attachments as $attachment) {
                    $fullPath = storage_path('app/private/' . $attachment['path']);
                    Log::info("Attaching file from path: $fullPath");

                    $message->attach($fullPath, [
                        'as' => $attachment['name'],
                        'mime' => $attachment['mime'],
                    ]);
                }
            });
        }
    }
}
