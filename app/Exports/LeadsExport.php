<?php
// app/Exports/LeadsExport.php

namespace App\Exports;

use App\Models\Leads;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Cell\Hyperlink;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;


class LeadsExport implements FromCollection, WithHeadings
{
    protected $leads;

    public function __construct($leads)
    {
        $this->leads = $leads;
    }

    public function collection()
    {
        return collect($this->leads)->map(function($lead) {
            $statusText = '';
            switch ($lead->ti_status) {
                case 1:
                    $statusText = 'Complete';
                    break;
                case 0:
                    $statusText = 'Pending';
                    break;
                case 2:
                    $statusText = 'Meeting';
                    break;
                case 3:
                    $statusText = 'Other';
                    break;
            }

            return [
                'Company  Name' => $lead->business->name ?? 'N/A',
                'Lead Full Name' => $lead->business->owner_full_name ?? 'N/A',
                'Lead Email' => $lead->business->owner_email ?? 'N/A',
                'Lead Number' =>$lead->business->owner_number ?? 'N/A',
                'Lead Address' =>$lead->business->address ?? 'N/A',
                'Lead Area' =>$lead->business->area ?? 'N/A',
                'Lead Pincode' =>$lead->business->pincode ?? 'N/A',
                'Lead City' =>$lead->business->city ?? 'N/A',
                'Lead State' =>$lead->business->state ?? 'N/A',
                'Lead Country' =>$lead->business->country ?? 'N/A',
                'Assigned To' => $lead->user ? $lead->user->first_name . ' ' . $lead->user->last_name : 'Not Assigned',
                'User Email' => $lead->user ? $lead->user->email : 'N/A',
                'User Mobile Number' =>$lead->user ? $lead->user->phone_number : 'N/A',
                'Status' => $statusText,
                'Visit Date' => $lead->visit_date,
                'Created At' => $lead->created_at->format('Y-m-d H:i:s'),
            ];
        });
    }
    

    public function headings(): array
    {
        return [
            'Company Name',
            'Lead Full Name',
            'Lead Email',
            'Lead Mobile Number',
            'Lead Address',
            'Lead Area' ,
            'Lead Pincode' ,
            'Lead City' ,
            'Lead State' ,
            'Lead Country' ,  
            'Assigned User To',
            'User Email',
            'User Mobile Number',
            'Status',
            'Visit Date',
            'Created At',
        ];
    }
  
   
}
