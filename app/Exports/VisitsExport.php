<?php

namespace App\Exports;

use App\Models\Leads;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class VisitsExport implements FromCollection, WithHeadings
{
    protected $interval;
    protected $memberId;

    public function __construct($interval, $memberId)
    {
        $this->interval = $interval;
        $this->memberId = $memberId;
    }

    public function collection()
    {
        // Fetch the data based on the interval and member ID
        $visitsData = $this->getVisitsData($this->interval, $this->memberId);

        return collect($visitsData);
    }

    public function headings(): array
    {
        return [
            'Date',
            'User Full Name',
            'User Email',
            'User Phone',
            'Assigned Date',
            'Visit Date',
            'Company Name',
            'Lead Full Name',
            'Lead Email',
            'Lead Phone',
            'Lead Address',
            'Lead Area',
            'Lead City',
            'Lead State',
            'TI Status'
        ];
    }

    private function getVisitsData($interval, $memberId)
    {
        $query = Leads::query()
            ->select(
                'leads.created_at as Date',
                \DB::raw('CONCAT(users.first_name, " ", users.last_name) as UserName'),
                'users.email as UserEmail',
                'users.phone_number as UserPhone',
                'leads.created_at as AssignedDate',
                'leads.visit_date as VisitDate',
                'business.name as BusinessName',
                'business.owner_full_name as OwnerFullName',
                'business.owner_email as OwnerEmail',
                'business.owner_number as OwnerPhone',
                'business.address as BusinessAddress',
                'business.area as BusinessArea',
                'business.city as BusinessCity',
                'business.state as BusinessState',
                'leads.ti_status as TIStatus'
            )
            ->join('users', 'users.id', '=', 'leads.team_id')
            ->join('business', 'business.id', '=', 'leads.business_id');

        if ($memberId !== 'all') {
            $query->where('leads.team_id', $memberId);
        }

        switch ($interval) {
            case 'week':
                $startDate = Carbon::now()->startOfWeek();
                $endDate = Carbon::now()->endOfWeek();
                break;
            case 'month':
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                break;
            case 'all':
                $startDate = Carbon::minValue();
                $endDate = Carbon::maxValue();
                break;
            default:
                $startDate = Carbon::now()->startOfWeek();
                $endDate = Carbon::now()->endOfWeek();
        }

        $query->whereBetween('leads.created_at', [$startDate, $endDate]);

        return $query->get();
    }
}
