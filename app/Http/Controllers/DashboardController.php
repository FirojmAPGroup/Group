<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Business;
use App\Models\Leads;
use Carbon\Carbon;
use App\Models\User;
use App\Exports\VisitsExport;
use Maatwebsite\Excel\Facades\Excel;

class DashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        $interval = $request->input('interval', 'today');
        $selectedMemberId = $request->input('member_id', 'all');
    
        // Retrieve visits data based on interval
        $visitsData = $this->getVisitsData($interval, $selectedMemberId);
    
        // If it's an AJAX request, return JSON response
        if ($request->ajax()) {
            return response()->json([
                'labels' => $visitsData['labels'],
                'totalVisits' => $visitsData['totalVisits'],
                'completedVisits' => $visitsData['completedVisits'],
                'pendingVisits' => $visitsData['pendingVisits'],
                'unassignedVisits' => $visitsData['unassignedVisits'],
            ]);
        }
    
        // Otherwise, return the view with data for rendering
        $teamMembers = User::whereDoesntHave('roles')
                            ->orWhereHas('roles', function ($query) {
                                $query->where('name', 'user');
                            })
                            ->get();
    
        $leadsQuery = Leads::query();
        if ($selectedMemberId !== 'all' && $selectedMemberId !== '') {
            $leadsQuery->where('team_id', $selectedMemberId);
        }
        $leads = $leadsQuery->get();
    
        return view('dashboard.index', [
            'urlListData' => routePut('app.todayVisits'),
            'table' => 'tableLeads',
            'totalVisits' => $visitsData['totalVisits'],
            'completedVisits' => $visitsData['completedVisits'],
            'pendingVisits' => $visitsData['pendingVisits'],
            'unassignedVisits' => $visitsData['unassignedVisits'],
            'chartLabels' => $visitsData['labels'],
            'interval' => $interval,
            'teamMembers' => $teamMembers,
            'selectedMemberId' => $selectedMemberId,
            'leads' => $leads,
        ]);
    }
    public function getData(Request $request)
    {
        $interval = $request->input('interval');
        $memberId = $request->input('member_id');

        $visitsData = $this->getVisitsData($interval, $memberId);

        return response()->json($visitsData);
    }
    public function exportData(Request $request)
    {
        $interval = $request->input('interval', 'week');
        $memberId = $request->input('member_id', 'all');

        return Excel::download(new VisitsExport($interval, $memberId), 'visits_data.xlsx');
    }
    
    // Function to retrieve visits data based on interval and selected member ID
    private function getVisitsData($interval, $selectedMemberId)
    {
        switch ($interval) {
            case 'today':
                return $this->getVisitsDataForDay($selectedMemberId);
            case 'week':
                return $this->getVisitsDataForWeek($selectedMemberId);
            case 'month':
                return $this->getVisitsDataForMonth($selectedMemberId);
            case 'all':
                return $this->getAllVisitsData($selectedMemberId);
            default:
                return $this->getVisitsDataForDay($selectedMemberId); // Default to today's data
        }
    }
    
    private function getVisitsDataForDay($selectedMemberId)
    {
        $date = Carbon::now(); // Get today's date
        $labels = [$date->format('l, F j')]; // Day name
        $totalVisits = Leads::whereDate('created_at', $date)
                            ->when($selectedMemberId !== 'all' && $selectedMemberId !== '', function ($query) use ($selectedMemberId) {
                                return $query->where('team_id', $selectedMemberId);
                            })
                            ->count();
        $completedVisits = Leads::whereDate('created_at', $date)
                                ->where('ti_status', 1)
                                ->when($selectedMemberId !== 'all' && $selectedMemberId !== '', function ($query) use ($selectedMemberId) {
                                    return $query->where('team_id', $selectedMemberId);
                                })
                                ->count();
        $pendingVisits = Leads::whereDate('created_at', $date)
                                ->where('ti_status', '!=', 1)
                                ->when($selectedMemberId !== 'all' && $selectedMemberId !== '', function ($query) use ($selectedMemberId) {
                                    return $query->where('team_id', $selectedMemberId);
                                })
                                ->count();
        $unassignedVisits = Business::whereDate('created_at', $date)
                                    ->when($selectedMemberId !== 'all' && $selectedMemberId !== '', function ($query) use ($selectedMemberId) {
                                        return $query->where('id', $selectedMemberId);
                                    })
                                    ->count();
    
        // Return an array with the visit data and labels
        return [
            'totalVisits' => $totalVisits,
            'completedVisits' => $completedVisits,
            'pendingVisits' => $pendingVisits,
            'unassignedVisits' => $unassignedVisits,
            'labels' => $labels,
        ];
    }
    
    private function getVisitsDataForWeek($selectedMemberId)
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
            $totalVisits[] = Leads::whereDate('created_at', $date)
                                ->when($selectedMemberId !== 'all' && $selectedMemberId !== '', function ($query) use ($selectedMemberId) {
                                    return $query->where('team_id', $selectedMemberId);
                                })
                                ->count();
            $completedVisits[] = Leads::whereDate('created_at', $date)
                                    ->where('ti_status', 1)
                                    ->when($selectedMemberId !== 'all' && $selectedMemberId !== '', function ($query) use ($selectedMemberId) {
                                        return $query->where('team_id', $selectedMemberId);
                                    })
                                    ->count();
            $pendingVisits[] = Leads::whereDate('created_at', $date)
                                ->where('ti_status', '!=', 1)
                                ->when($selectedMemberId !== 'all' && $selectedMemberId !== '', function ($query) use ($selectedMemberId) {
                                    return $query->where('team_id', $selectedMemberId);
                                })
                                ->count();
            $unassignedVisits[] = Business::whereDate('created_at', $date)
                                    ->when($selectedMemberId !== 'all' && $selectedMemberId !== '', function ($query) use ($selectedMemberId) {
                                        return $query->where('id', $selectedMemberId);
                                    })
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
    
    private function getVisitsDataForMonth($selectedMemberId)
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
            $totalVisits[] = Leads::whereDate('created_at', $date)
                                ->when($selectedMemberId !== 'all' && $selectedMemberId !== '', function ($query) use ($selectedMemberId) {
                                    return $query->where('team_id', $selectedMemberId);
                                })
                                ->count();
            $completedVisits[] = Leads::whereDate('created_at', $date)
                                    ->where('ti_status', 1)
                                    ->when($selectedMemberId !== 'all' && $selectedMemberId !== '', function ($query) use ($selectedMemberId) {
                                        return $query->where('team_id', $selectedMemberId);
                                    })
                                    ->count();
            $pendingVisits[] = Leads::whereDate('created_at', $date)
                                ->where('ti_status', '!=', 1)
                                ->when($selectedMemberId !== 'all' && $selectedMemberId !== '', function ($query) use ($selectedMemberId) {
                                    return $query->where('team_id', $selectedMemberId);
                                })
                                ->count();
            $unassignedVisits[] = Business::whereDate('created_at', $date)
                                    ->when($selectedMemberId !== 'all' && $selectedMemberId !== '', function ($query) use ($selectedMemberId) {
                                        return $query->where('id', $selectedMemberId);
                                    })
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
    
    private function getAllVisitsData($selectedMemberId)
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
                                  ->when($selectedMemberId !== 'all' && $selectedMemberId !== '', function ($query) use ($selectedMemberId) {
                                      return $query->where('id', $selectedMemberId);
                                  })
                                  ->count();
            $completedVisits[] = Leads::whereYear('visit_date', $date->year)
                                      ->whereMonth('visit_date', $date->month)
                                      ->where('ti_status', 1)
                                      ->when($selectedMemberId !== 'all' && $selectedMemberId !== '', function ($query) use ($selectedMemberId) {
                                          return $query->where('team_id', $selectedMemberId);
                                      })
                                      ->count();
            $pendingVisits[] = Leads::whereYear('created_at', $date->year)
                                    ->whereMonth('created_at', $date->month)
                                    ->where('ti_status', '!=', 1)
                                    ->when($selectedMemberId !== 'all' && $selectedMemberId !== '', function ($query) use ($selectedMemberId) {
                                        return $query->where('team_id', $selectedMemberId);
                                    })
                                    ->count();
            $unassignedVisits[] = Business::whereYear('created_at', $date->year)
                                           ->whereMonth('created_at', $date->month)
                                           ->when($selectedMemberId !== 'all' && $selectedMemberId !== '', function ($query) use ($selectedMemberId) {
                                               return $query->where('id', $selectedMemberId);
                                           })
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


