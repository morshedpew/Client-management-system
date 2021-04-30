<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Mail;
use App\Mail\MeetingReminderEmail;

use App\Meeting;

use Carbon\Carbon;

class SendMeetingReminder extends Command
{
    protected $signature = 'command:sendmeetingreminder';

    protected $description = 'Send meeting reminder according to meeting date';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $meetings = Meeting::where('status', '=', 'Pending')->get();

        foreach($meetings as $meeting)
        {
            $meeting_date = new Carbon($meeting->date, 'Asia/Dhaka');
            $meeting_time = new Carbon($meeting->time, 'Asia/Dhaka');

            $today = Carbon::today('Asia/Dhaka');
            $now = Carbon::now('Asia/Dhaka');

            if($meeting_date->greaterThan($today) && $meeting_date->diffInDays($today) == 1)
            {
                Mail::to($meeting->client->person->email)->send(new MeetingReminderEmail($meeting, 1));
            }
            else if($meeting_date->equalTo($today))
            {
                if($meeting_time->greaterThan($now) && $meeting_time->diffInMinutes($now) < 180)
                {
                    Mail::to($meeting->client->person->email)->send(new MeetingReminderEmail($meeting, 2));
                }
                else if($meeting_time->lessThan($now) && $meeting_time->diffInMinutes($now) > 20)
                {
                    $meeting->status = "Expired";
                    $meeting->save();
                }
            }
        }
    }
}
