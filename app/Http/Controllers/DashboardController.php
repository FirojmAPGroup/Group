<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Business;
use App\Models\Leads;
use Carbon\Carbon;
use App\Models\User;
class DashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        $interval = $request->input('interval', 'today');
        $visitsData = $this->getVisitsData($interval);

        if ($request->ajax()) {
            return response()->json($visitsData);
        }
        $teamMembers = User::whereDoesntHave('roles')
        ->orWhereHas('roles', function ($query) {
            $query->where('name', 'user');
        })
        ->get();
        return view('dashboard.index', [
            'urlListData' => routePut('app.todayVisits'),
            'table' => 'tableLeads',
            'totalVisits' => $visitsData['totalVisits'],
            'completedVisits' => $visitsData['completedVisits'],
            'pendingVisits' => $visitsData['pendingVisits'],
            'unassignedVisits' => $visitsData['unassignedVisits'],
            'chartLabels' => $visitsData['labels'], // Added labels
            'interval' => $interval,
            'teamMembers'=>$teamMembers
        ]);
    }

    private function getVisitsData($interval)
    {
        switch ($interval) {
            case 'today':
                return $this->getVisitsDataForDay(); // Pass current date for 'today'
            case 'week':
                return $this->getVisitsDataForWeek();
            case 'month':
                return $this->getVisitsDataForMonth();
            case 'all':
                return $this->getAllVisitsData();
            default:
                return $this->getVisitsDataForDay(); // Default to today's data
        }
    }

    private function getVisitsDataForDay()
    {
        $date = Carbon::now()->toDateString(); // Get today's date

        $totalVisits = Leads::whereDate('created_at', $date)->count();
        $completedVisits = Leads::whereDate('visit_date', $date)->where('ti_status', 1)->count();
        $pendingVisits = Leads::whereDate('created_at', $date)->where('ti_status', 2)->count();
        $unassignedVisits = Business::whereDate('created_at', $date)->where('ti_status', 0)->count();

        // Return an array with the visit data and labels
        return [
            'totalVisits' => $totalVisits,
            'completedVisits' => $completedVisits,
            'pendingVisits' => $pendingVisits,
            'unassignedVisits' => $unassignedVisits,
            'labels' => [$date], // Provide the current date as the label
        ];
    }



    private function getVisitsDataForWeek()
    {
        $startDate = Carbon::now()->startOfWeek();
        $endDate = Carbon::now()->endOfWeek();

        $labels = [];
        $totalVisits = [];
        $completedVisits = [];
        $pendingVisits = [];
        $unassignedVisits = [];

        for ($date = $startDate; $date <= $endDate; $date->addDay()) {
            $labels[] = $date->format('l, F j'); // Day name
            $totalVisits[] =Business::whereDate('created_at', $date)->count();
            $completedVisits[] = Leads::whereDate('created_at', $date)->where('ti_status', 1)->count();
            $pendingVisits[] = Leads::whereDate('created_at', $date)->where('ti_status', 2)->count();
            $unassignedVisits[] = Business::whereDate('created_at', $date)->where('ti_status', 0)->count();
        }

        return [
            'labels' => $labels,
            'totalVisits' => $totalVisits,
            'completedVisits' => $completedVisits,
            'pendingVisits' => $pendingVisits,
            'unassignedVisits' => $unassignedVisits,
        ];
    }

    private function getVisitsDataForMonth()
    {
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        $labels = [];
        $totalVisits = [];
        $completedVisits = [];
        $pendingVisits = [];
        $unassignedVisits = [];

        for ($date = $startDate; $date <= $endDate; $date->addDay()) {
            $labels[] = $date->format('j F'); // Day name
            $totalVisits[] = Leads::whereDate('created_at', $date)->count();
            $completedVisits[] = Leads::whereDate('visit_date', $date)->where('ti_status', 1)->count();
            $pendingVisits[] = Leads::whereDate('created_at', $date)->where('ti_status', 2)->count();
            $unassignedVisits[] = Business::whereDate('created_at', $date)->where('ti_status', 0)->count();
        }

        return [
            'labels' => $labels,
            'totalVisits' => $totalVisits,
            'completedVisits' => $completedVisits,
            'pendingVisits' => $pendingVisits,
            'unassignedVisits' => $unassignedVisits,
        ];
    }
    private function getAllVisitsData()
    {
        $startDate = Carbon::now()->startOfYear();
        $endDate = Carbon::now()->endOfYear();
    
        $labels = [];
        $totalVisits = [];
        $completedVisits = [];
        $pendingVisits = [];
        $unassignedVisits = [];
    
        for ($date = $startDate; $date <= $endDate; $date->addMonth()) {
            $labels[] = $date->format('F'); // Only month name
            $totalVisits[] = Business::whereYear('created_at', $date->year)
                                  ->whereMonth('created_at', $date->month)
                                  ->count();
            $completedVisits[] = Leads::whereYear('visit_date', $date->year)
                                      ->whereMonth('visit_date', $date->month)
                                      ->where('ti_status', 1)
                                      ->count();
            $pendingVisits[] = Leads::whereYear('created_at', $date->year)
                                    ->whereMonth('created_at', $date->month)
                                    ->where('ti_status', 2)
                                    ->count();
            $unassignedVisits[] = Business::whereYear('created_at', $date->year)
                                           ->whereMonth('created_at', $date->month)
                                           ->where('ti_status', 0)
                                           ->count();
        }
    
        return [
            'labels' => $labels,
            'totalVisits' => $totalVisits,
            'completedVisits' => $completedVisits,
            'pendingVisits' => $pendingVisits,
            'unassignedVisits' => $unassignedVisits,
        ];
    }
    
}
