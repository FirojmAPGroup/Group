<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function dashboard(){
        return view('dashboard.index',[
            'urlListData'=>routePut('app.todayVisits'),'table' => 'tableLeads'
        ]
    );
    }
}
