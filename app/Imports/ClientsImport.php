<?php

namespace App\Imports;

use App\{Client, Source, Service, Person, LeadStatus};
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;

use Carbon\Carbon;

class ClientsImport implements ToCollection, WithHeadingRow, WithChunkReading
{
    public $sources, $services, $persons, $leadStatuses;

    public function __construct()
    {
        $this->sources = Source::all();
        $this->services = Service::all();
        $this->persons = Person::all();
        $this->leadStatuses = LeadStatus::all();
    }

    public function collection(Collection $rows)
    {
        foreach($rows as $row)
        {
            $client = new Client;

            $today = Carbon::today('Asia/Dhaka');

            $client->custom_id = "CL-".$today->day;

            $client->name = $row['client_name'];
            $client->company_name = $row['company_name'];
            $client->conversion_date = $row['conversion_date'];
            $client->contact_number = $row['contact_number'];
            $client->email = $row['email'];
            $client->address = $row['address'];
            $client->comment_1 = $row['comment_1'];
            $client->comment_2 = $row['comment_2'];

            foreach($this->sources as $source)
            {
                if($source->name == $row['source'])
                {
                    $client->source_id = $source->id;
                    break;
                }
            }

            foreach($this->services as $service)
            {
                if($service->name == $row['service_requirement'])
                {
                    $client->service_id = $service->id;
                    break;
                }
            }

            foreach($this->persons as $person)
            {
                if($person->name == $row['assigned_person'])
                {
                    $client->person_id = $person->id;
                    break;
                }
            }

            foreach($this->leadStatuses as $leadStatus)
            {
                if($leadStatus->name == $row['lead_status'])
                {
                    $client->lead_status_id = $leadStatus->id;
                    break;
                }
            }

            $client->save();

            $client->custom_id = $client->custom_id.$client->id;

            $client->save();
        }
    }

    public function chunkSize(): int
    {
        return 100;
    }
}
