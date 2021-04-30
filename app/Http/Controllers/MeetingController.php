<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\MeetingFormRequest;
use App\Http\Requests\MeetingEditFormRequest;

use App\{Meeting, Client, Person, LeadStatus, Service};

use Carbon\Carbon;

class MeetingController extends Controller
{
    public function index()
    {
        $today = Carbon::today('Asia/Dhaka');
        $pre_from_date = $today->subDays(7);
        $pre_from_date = $pre_from_date->toDateString();

        $today = Carbon::today('Asia/Dhaka');
        $pre_to_date = $today->addDays(7);
        $pre_to_date = $pre_to_date->toDateString();

        $meetings = Meeting::whereBetween('date', [$pre_from_date, $pre_to_date])
                                ->with('client')
                                ->orderBy('date')
                                ->orderBy('time')
                                ->get();

        return view('meetings', [
            'meetings' => $meetings,
            'serial' => 0,

            'lead_statuses' => LeadStatus::all(),
            'services' => Service::all(),
            'persons' => Person::all(),

            'pre_from_date' => $pre_from_date,
            'pre_to_date' => $pre_to_date
        ]);
    }

    public function filterMeetings(Request $request)
    {
        /* $this->validate($request, [
            'from_date' => 'required',
            'to_date' => 'required',
            'from_time' => 'required',
            'to_time' => 'required',
            'meeting_status' => 'required'
        ]); */

        $meeting_status_values = explode(", ", $request->meeting_status);

        $meetings = Meeting::whereBetween('date', [$request->from_date, $request->to_date])
                                ->whereBetween('time', [$request->from_time, $request->to_time])
                                ->whereIn('status', $meeting_status_values)
                                ->with('client')
                                ->orderBy('date')
                                ->orderBy('time')
                                ->get();

        $temp_meetings = array();

        if($request->lead_status != 1)
        {
            foreach($meetings as $meeting)
            {
                if($meeting->client->leadStatus->name == $request->lead_status)
                {
                    $temp_meetings[] = $meeting;
                }
            }

            $meetings = $temp_meetings;
            $temp_meetings = [];
        }

        if($request->service != 1)
        {
            foreach($meetings as $meeting)
            {
                if($meeting->client->service->name == $request->service)
                {
                    $temp_meetings[] = $meeting;
                }
            }

            $meetings = $temp_meetings;
            $temp_meetings = [];
        }

        if($request->assigned_person != 1)
        {
            foreach($meetings as $meeting)
            {
                if($meeting->client->person->name == $request->assigned_person)
                {
                    $temp_meetings[] = $meeting;
                }
            }

            $meetings = $temp_meetings;
            $temp_meetings = [];
        }

        if($request->client_id != "")
        {
            foreach($meetings as $meeting)
            {
                if($meeting->client->custom_id == $request->client_id)
                {
                    $temp_meetings[] = $meeting;
                }
            }

            $meetings = $temp_meetings;
            $temp_meetings = [];
        }

        return view('meetings', [
            'meetings' => $meetings,
            'serial' => 0,

            'lead_statuses' => LeadStatus::all(),
            'services' => Service::all(),
            'persons' => Person::all(),

            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'from_time' => $request->from_time,
            'to_time' => $request->to_time,

            'old_meeting_status' => $request->meeting_status,
            'old_lead_status' => $request->lead_status,
            'old_service' => $request->service,
            'old_assigned_person' => $request->assigned_person,
            'old_client_id' => $request->client_id
        ]);
    }

    public function addMeetingView()
    {
        $clients = Client::all();

        return view('addmeeting', compact('clients'));
    }

    public function addMeeting(MeetingFormRequest $request)
    {
        $meeting = new Meeting;

        $meeting->title = $request->title;
        $meeting->agenda = $request->agenda;

        if($request->client_id != null)
        {
            $meeting->client_id = $request->client_id;
        }
        else
        {
            return redirect()->back()->with([
                'error_client_custom_id' => 'Please enter a valid client ID',
                'title' => $request->title,
                'agenda' => $request->agenda,
                'date' => $request->date,
                'time' => $request->time
            ]);
        }

        $meeting->date = $request->date;
        $meeting->time = $request->time;
        $meeting->status = "Pending";

        $meeting->save();

        return redirect()->back()->withStatus('Meeting Created Successfully !');
    }

    public function removeMeeting($meeting_id)
    {
        $meeting = Meeting::find($meeting_id);

        $meeting->delete();

        return redirect()->back();
    }

    public function editMeetingView($meeting_id)
    {
        $meeting = Meeting::with('client')->find($meeting_id);

        return view('editmeeting', [
            'meeting' => $meeting,
            'persons' => Person::all(),
            'leadStatuses' => LeadStatus::all()
        ]);
    }

    public function editMeeting(MeetingEditFormRequest $request, $meeting_id)
    {
        $meeting = Meeting::find($meeting_id);

        $client = Client::find($meeting->client->id);

        $meeting->title = $request->title;
        $meeting->agenda = $request->agenda;
        $meeting->date = $request->date;
        $meeting->time = $request->time;

        $client->person_id = $request->person_id;
        $client->lead_status_id = $request->lead_status_id;

        $meeting->save();

        $client->save();

        return redirect()->back()->withStatus('Meeting Updated Successfully !');
    }




    public function checkClient(Request $request)
    {
        $clients = Client::where('custom_id', '=', $request->custom_id)->get();

        if(count($clients) == 1)
        {
            return [
                'count' => 1,
                'name' => $clients[0]['name'],
                'id' => $clients[0]['id']
            ];
        }
        else if(count($clients) == 0)
        {
            return [
                'count' => 0,
                'msg' => 'Invalid ID, Client not found'
            ];
        }
    }
}
