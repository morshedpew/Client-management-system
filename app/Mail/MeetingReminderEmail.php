<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class MeetingReminderEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $meeting, $value;

    public function __construct($meeting, $value)
    {
        $this->meeting = $meeting;
        $this->value = $value;
    }

    public function build()
    {
        return $this->view('email.meetingreminderemail');
    }
}
