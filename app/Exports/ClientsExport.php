<?php

namespace App\Exports;

use App\Client;

use Maatwebsite\Excel\Concerns\FromCollection;
//use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;

use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ClientsExport implements FromCollection, ShouldAutoSize, WithMapping, WithHeadings, WithStyles, WithColumnFormatting
{
    public $serial, $criteria;

    public function __construct($criteria)
    {
        $this->serial = 0;
        $this->criteria = $criteria;
    }

    public function collection()
    {
        $clients = Client::whereBetween('conversion_date', [$this->criteria[0], $this->criteria[1]])
                            ->with(['source', 'person', 'service', 'leadstatus'])
                            ->orderBy('conversion_date')
                            ->get();

        if($this->criteria[2] != 1)
        {
            $temp_clients = collect([]);

            foreach($clients as $client)
            {
                if($client->leadStatus->name == $this->criteria[2])
                {
                    $temp_clients->push($client);
                }
            }

            $clients = $temp_clients;
        }

        if($this->criteria[3] != 1)
        {
            $temp_clients = collect([]);

            foreach($clients as $client)
            {
                if($client->source->name == $this->criteria[3])
                {
                    $temp_clients->push($client);
                }
            }

            $clients = $temp_clients;
        }

        if($this->criteria[4] != 1)
        {
            $temp_clients = collect([]);

            foreach($clients as $client)
            {
                if($client->service->name == $this->criteria[4])
                {
                    $temp_clients->push($client);
                }
            }

            $clients = $temp_clients;
        }

        if($this->criteria[5] != 1)
        {
            $temp_clients = collect([]);

            foreach($clients as $client)
            {
                if($client->person->name == $this->criteria[5])
                {
                    $temp_clients->push($client);
                }
            }

            $clients = $temp_clients;
        }

        return $clients;
    }

    public function map($client): array
    {
        $this->serial++;

        return [
            $this->serial,
            $client->custom_id,
            $client->name,
            $client->company_name,
            $client->conversion_date,
            $client->source->name,
            $client->person->name,
            $client->service->name,
            $client->contact_number,
            $client->email,
            $client->address,
            $client->leadstatus->name,
            $client->comment_1,
            $client->comment_2
        ];
    }

    public function headings(): array
    {
        return [
            'Sl. No.',
            'ID',
            'Client Name',
            'Company Name',
            'Conversion Date',
            'Source',
            'Assigned Person',
            'Service Requirement',
            'Contact Number',
            'Email',
            'Address',
            'Lead Status',
            'Comment-1',
            'Comment-2'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1    => ['font' => [
                'bold' => true,
                'italic' => true,
                'size' => 16
            ]],
        ];
    }

    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_DATE_YYYYMMDD
        ];
    }
}
