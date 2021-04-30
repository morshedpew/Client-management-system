<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ClientFormRequest;
use App\Http\Requests\ClientEditFormRequest;

use App\{Client, Source, Service, Person, LeadStatus};

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ClientsExport;
use App\Imports\ClientsImport;

use Carbon\Carbon;

class ClientController extends Controller
{
    public function index()
    {
        $today = Carbon::today('Asia/Dhaka');
        $pre_from_date = $today->subDays(14);
        $pre_from_date = $pre_from_date->toDateString();

        $today = Carbon::today('Asia/Dhaka');
        $pre_to_date = $today->toDateString();

        $clients = Client::whereBetween('conversion_date', [$pre_from_date, $pre_to_date])
                            ->with(['source', 'service', 'person', 'leadstatus'])
                            ->orderBy('conversion_date')
                            ->get();

        return view('clients', [
            'clients' => $clients,
            'serial' => 0,

            'sources' => Source::all(),
            'services' => Service::all(),
            'persons' => Person::all(),
            'leadStatuses' => LeadStatus::all(),

            'pre_from_date' => $pre_from_date,
            'pre_to_date' => $pre_to_date
        ]);
    }

    public function filterClients(Request $request)
    {
        /* $this->validate($request, [
            'from_date' => 'required',
            'to_date' => 'required'
        ]); */

        $clients = Client::whereBetween('conversion_date', [$request->from_date, $request->to_date])
                                ->with(['source', 'service', 'person', 'leadstatus'])
                                ->orderBy('conversion_date')
                                ->get();

        $temp_clients = array();

        if($request->lead_status != 1)
        {
            foreach($clients as $client)
            {
                if($client->leadStatus->name == $request->lead_status)
                {
                    $temp_clients[] = $client;
                }
            }

            $clients = $temp_clients;
            $temp_clients = [];
        }

        if($request->source != 1)
        {
            foreach($clients as $client)
            {
                if($client->source->name == $request->source)
                {
                    $temp_clients[] = $client;
                }
            }

            $clients = $temp_clients;
            $temp_clients = [];
        }

        if($request->service != 1)
        {
            foreach($clients as $client)
            {
                if($client->service->name == $request->service)
                {
                    $temp_clients[] = $client;
                }
            }

            $clients = $temp_clients;
            $temp_clients = [];
        }

        if($request->assigned_person != 1)
        {
            foreach($clients as $client)
            {
                if($client->person->name == $request->assigned_person)
                {
                    $temp_clients[] = $client;
                }
            }

            $clients = $temp_clients;
            $temp_clients = [];
        }

        return view('clients', [
            'clients' => $clients,
            'serial' => 0,

            'sources' => Source::all(),
            'services' => Service::all(),
            'persons' => Person::all(),
            'leadStatuses' => LeadStatus::all(),

            'from_date' => $request->from_date,
            'to_date' => $request->to_date,

            'old_lead_status' => $request->lead_status,
            'old_source' => $request->source,
            'old_service' => $request->service,
            'old_assigned_person' => $request->assigned_person
        ]);
    }

    public function addClientView()
    {
        return view('addclient', [
            'sources' => Source::all(),
            'services' => Service::all(),
            'persons' => Person::all(),
            'leadStatuses' => LeadStatus::all()
        ]);
    }

    public function addClient(ClientFormRequest $request)
    {
        $client = new Client;

        $today = Carbon::today('Asia/Dhaka');

        $client->custom_id = "CL-".$today->day;

        $client->name = $request->client_name;
        $client->company_name = $request->company_name;
        $client->conversion_date = $request->conversion_date;

        $client->contact_number = $request->contact_number;
        $client->email = $request->email;
        $client->address = $request->address;

        $client->comment_1 = $request->comment_1;
        $client->comment_2 = $request->comment_2;

        $client->source_id = $request->source_id;
        $client->service_id = $request->service_id;
        $client->person_id = $request->assigned_person_id;
        $client->lead_status_id = $request->lead_status_id;

        $client->save();

        $client->custom_id = $client->custom_id.$client->id;

        $client->save();

        return redirect()->back()->withStatus('Client Added Successfully !');
    }

    public function searchClientView()
    {
        return view('searchclient');
    }

    public function searchClient(Request $request)
    {
        $this->validate($request, [
            'search_index' => 'required | alpha'
        ], [
            'search_index.required' => 'Please enter some keywords to find a match.'
        ]);

        $clients = Client::where('name', 'LIKE', $request->search_index."%")->get();

        if(count($clients) > 0)
        {
            return view('searchclient', [
                'clients' => $clients,
                'serial' => 0,
                'old_search_index' => $request->search_index,
                'msg' => 'Match Found !'
            ]);
        }
        else
        {
            return view('searchclient', [
                'old_search_index' => $request->search_index,
                'msg' => 'No Match Found !'
            ]);
        }
    }

    public function removeClient($client_id, $type)
    {
        $client = Client::find($client_id);

        $client->delete();

        if($type == 1)
        {
            return redirect()->back();
        }
        else if($type == 2)
        {
            return redirect(route('client.search'));
        }
    }

    public function editClientAjax(Request $request)
    {
        $client = Client::find($request->id);

        $client->name = $request->client_name;
        $client->company_name = $request->company_name;
        $client->conversion_date = $request->conversion_date;

        $client->contact_number = $request->contact_number;
        $client->email = $request->email;
        $client->address = $request->address;

        $client->comment_1 = $request->comment_1;
        $client->comment_2 = $request->comment_2;

        $client->source_id = $request->source_id;
        $client->service_id = $request->service_id;
        $client->person_id = $request->assigned_person_id;
        $client->lead_status_id = $request->lead_status_id;

        $client->save();

        return "Updated Successfully !";
    }




    public function editClientView($client_id)
    {
        $client = Client::find($client_id);

        return view('editclient', [
            'client' => $client,
            'sources' => Source::all(),
            'services' => Service::all(),
            'persons' => Person::all(),
            'leadStatuses' => LeadStatus::all()
        ]);
    }

    public function editClient(ClientEditFormRequest $request, $client_id)
    {
        $client = Client::find($client_id);

        $client->name = $request->client_name;
        $client->company_name = $request->company_name;
        $client->conversion_date = $request->conversion_date;


        $client_contact_numbers = Client::where('contact_number', '=', $request->contact_number)->get();

        if(count($client_contact_numbers) == 0)
        {
            $client->contact_number = $request->contact_number;
        }
        else if(count($client_contact_numbers) == 1 && $client_contact_numbers[0]['contact_number'] != $client->contact_number)
        {
            return redirect()->back()->with(['error_contact_number' => 'The phone number - '.$request->contact_number.' already exists.']);
        }


        $client_emails = Client::where('email', '=', $request->email)->get();

        if(count($client_emails) == 0)
        {
            $client->email = $request->email;
        }
        else if(count($client_emails) == 1 && $client_emails[0]['email'] != $client->email)
        {
            return redirect()->back()->with(['error_email' => 'The email - '.$request->email.' already exists.']);
        }


        $client->address = $request->address;

        $client->comment_1 = $request->comment_1;
        $client->comment_2 = $request->comment_2;

        $client->source_id = $request->source_id;
        $client->service_id = $request->service_id;
        $client->person_id = $request->assigned_person_id;
        $client->lead_status_id = $request->lead_status_id;

        $client->save();

        return redirect()->back()->withStatus('Client Details Saved !');
    }




    public function uploadClients()
    {
        return view('uploadclients');
    }

    public function import(Request $request)
    {
        $this->validate($request, [
            'my_file' => 'required | mimes:xls,xlsx'
        ], [
            'my_file.required' => 'Please choose a file to upload.',
            'my_file.mimes' => 'File type must be of xlsx or xls.'
        ]);

        $file = $request->my_file->store('excel_import');

        Excel::import(new ClientsImport, $file);

        return redirect()->back()->withStatus('Excel File Uploaded Successfully !');
    }

    public function export(Request $request)
    {
        $criteria = explode(", ", $request->download_criteria);

        $fileName = "Clients.xlsx";

        return Excel::download(new ClientsExport($criteria), $fileName);
    }
}
