<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Leads;
class DashboardController extends Controller
{
    public function dashboard(){
        $totalVisits = Leads::TotalVisits();
        $completedVisits = Leads::completedVisit();
        $pendingVisits = Leads::pendingVisit();
        $unassignedVisits = Leads::unassignedVisit();
        return view('dashboard.index',[
            'urlListData'=>routePut('app.todayVisits'),'table' => 'tableLeads',
            'totalVisits'=>$totalVisits, 
            'completedVisits'=>$completedVisits,
            'pendingVisits'=>$pendingVisits,
            'unassignedVisits'=>$unassignedVisits
        ]
        
    );
    }
}
