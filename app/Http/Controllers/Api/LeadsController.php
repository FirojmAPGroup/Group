<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Business;
use Illuminate\Http\Request;
use App\Models\Leads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NewLeadNotification;
class LeadsController extends Controller
{
    // public function getPendingLeads(Request $request){
    //     $user = Auth::guard('api')->user();
    //     $userLatitude = $user->latitude;
    //     $userLongitude = $user->longitude;

    //     $teamId = $user->id;

    //     $leads = Leads::with('hasBusiness')
    //                     ->where('team_id', $teamId)
    //                     ->where('leads.ti_status', 0)
    //                     ->get();

    //     $leadDistances = [];

    //     foreach ($leads as $lead) {
    //         $distance = $this->haversineGreatCircleDistance(
    //             $userLatitude,
    //             $userLongitude,
    //             $lead->hasBusiness->latitude,
    //             $lead->hasBusiness->longitude
    //         );

    //         $leadDistances[] = [
    //             'lead' => $lead,
    //             'distance' => $distance
    //         ];
    //     }

    //     usort($leadDistances, function ($a, $b) {
    //         return $a['distance'] <=> $b['distance'];
    //     });

    //     $data = [];

    //     foreach ($leadDistances as $distanceInfo) {
    //         $lead = $distanceInfo['lead'];
    //         $business = $lead->hasBusiness;
    //         $fullName = ($user->first_name ?? 'N/A') . ' ' . ($user->last_name ?? 'N/A');
    //         $data[] = [
    //             'user_id' => $user->id ?? 'N/A',
    //             'first_name' => $user->first_name ?? 'N/A',
    //             'last_name' => $user->last_name ?? 'N/A',
    //             'email' => $user->email ?? 'N/A',
    //             'phone_number' => $user->phone_number ?? 'N/A',
    //             'distance' => round($distanceInfo['distance'], 2),
    //             'lead_id' => $lead->id ?? "N/A",
    //             'visit_date' => $lead->visit_date ?? "N/A",
    //             'business_id' => $business->id ?? 'N/A',
    //             'Name' => $business->name ?? 'N/A',
    //             'lead_first_name' => $business->owner_first_name ?? 'N/A',
    //             'lead_last_name' => $business->owner_last_name ?? 'N/A',
    //             'lead_email' => $business->owner_email ?? 'N/A',
    //             'lead_number' => $business->owner_number ?? 'N/A',
    //             'pincode' => $business->pincode ?? 'N/A',
    //         ];
    //     }

    //     return response()->json([
    //         'code' => 200,
    //         'data' => $data,
    //         'message' => 'Pending leads retrieved and sorted by distance successfully'
    //     ]);
     
    //     if($leads->count()){
    //         return response()->json([
    //             'code'=>200,
    //             'data'=>$leads,
    //             'message'=>'leads retrive successfully'
    //         ]);
    //     } else {
    //         return response()->json([
    //             'code'=>200,
    //             'data'=>[
    //                 'leads'=>[]
    //             ],
    //             'message'=>'leads not founds'
    //         ]);
    //     }

    // }
  
    public function getPendingLeads(Request $request){
        $user = Auth::guard('api')->user();
        $userLatitude = $user->latitude;
        $userLongitude = $user->longitude;
    
        $teamId = $user->id;
    
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        $lastWeekStart = Carbon::now()->subWeek()->startOfWeek();
        $lastWeekEnd = Carbon::now()->subWeek()->endOfWeek();
    
        $dateFilter = $request->input('date_filter', 'all'); // 'today', 'yesterday', 'both', 'last_week', or 'all'
    
        // Get count of all pending leads for the user's team
        $totalPendingLeadsCount = Leads::where('team_id', $teamId)
                                        ->where('leads.ti_status', [0,2])
                                        ->count();
    
        // Filter leads based on the date filter
        $leads = Leads::with('hasBusiness')
                        ->where('team_id', $teamId)
                        ->where('leads.ti_status', [0,2])
                        ->when($dateFilter === 'today', function ($query) use ($today) {
                            return $query->whereDate('created_at', $today);
                        })
                        ->when($dateFilter === 'yesterday', function ($query) use ($yesterday) {
                            return $query->whereDate('created_at', $yesterday);
                        })
                        ->when($dateFilter === 'both', function ($query) use ($today, $yesterday) {
                            return $query->whereDate('created_at', $today)
                                         ->orWhereDate('created_at', $yesterday);
                        })
                        ->when($dateFilter === 'last_week', function ($query) use ($lastWeekStart, $lastWeekEnd) {
                            return $query->whereBetween('created_at', [$lastWeekStart, $lastWeekEnd]);
                        })
                        ->get();
    
        $leadDistances = [];
    
        foreach ($leads as $lead) {
            $distance = $this->haversineGreatCircleDistance(
                $userLatitude,
                $userLongitude,
                $lead->hasBusiness->latitude,
                $lead->hasBusiness->longitude
            );
    
            $leadDistances[] = [
                'lead' => $lead,
                'distance' => $distance
            ];
        }
    
        usort($leadDistances, function ($a, $b) {
            return $a['distance'] <=> $b['distance'];
        });
    
        $data = [];
    
        foreach ($leadDistances as $distanceInfo) {
            $lead = $distanceInfo['lead'];
            $business = $lead->hasBusiness;
            $fullName = ($user->first_name ?? 'N/A') . ' ' . ($user->last_name ?? 'N/A');
    
            // Determine if the lead is from today, yesterday, or last week
            $createdAt = Carbon::parse($lead->created_at);
            $leadDate = $createdAt->isToday() ? 'today' :
                        ($createdAt->isYesterday() ? 'yesterday' :
                        ($createdAt->between($lastWeekStart, $lastWeekEnd) ? 'last_week' : 'other'));
    
            $data[] = [
                'user_id' => $user->id ?? 'N/A',
                'first_name' => $user->first_name ?? 'N/A',
                'last_name' => $user->last_name ?? 'N/A',
                'email' => $user->email ?? 'N/A',
                'phone_number' => $user->phone_number ?? 'N/A',
                'distance' => round($distanceInfo['distance'], 2),
                'lead_id' => $lead->id ?? "N/A",
                'visit_date' => $lead->visit_date ?? "N/A",
                'business_id' => $business->id ?? 'N/A',
                'Name' => $business->name ?? 'N/A',
                'lead_first_name' => $business->owner_first_name ?? 'N/A',
                'lead_last_name' => $business->owner_last_name ?? 'N/A',
                'lead_email' => $business->owner_email ?? 'N/A',
                'lead_number' => $business->owner_number ?? 'N/A',
                'pincode' => $business->pincode ?? 'N/A',
                'lead_date' => $leadDate // New field indicating 'today', 'yesterday', 'last_week', or 'other'
            ];
        }
    
        return response()->json([
            'code' => 200,
            'data' => $data,
            'date_filter' => $dateFilter,
            'total_pending_leads_count' => $totalPendingLeadsCount, // Total count of all pending leads
            'message' => 'Pending leads retrieved and sorted by distance successfully'
        ]);
    
        if($leads->count()){
            return response()->json([
                'code'=>200,
                'data'=>$leads,
                'message'=>'leads retrive successfully'
            ]);
        } else {
            return response()->json([
                'code'=>200,
                'data'=>[
                    'leads'=>[]
                ],
                'message'=>'leads not founds'
            ]);
        }

    }
    // public function getDoneLeads(){
    //     $user = Auth::guard('api')->user();
    //     $userLatitude = $user->latitude;
    //     $userLongitude = $user->longitude;

    //     $teamId = $user->id;

    //     $leads = Leads::with('hasBusiness')
    //                     ->where('team_id', $teamId)
    //                     ->where('leads.ti_status', 1)
    //                     ->get();

    //     $leadDistances = [];

    //     foreach ($leads as $lead) {
    //         $distance = $this->haversineGreatCircleDistance(
    //             $userLatitude,
    //             $userLongitude,
    //             $lead->hasBusiness->latitude,
    //             $lead->hasBusiness->longitude
    //         );

    //         $leadDistances[] = [
    //             'lead' => $lead,
    //             'distance' => $distance
    //         ];
    //     }

    //     usort($leadDistances, function ($a, $b) {
    //         return $a['distance'] <=> $b['distance'];
    //     });

    //     $data = [];

    //     foreach ($leadDistances as $distanceInfo) {
    //         $lead = $distanceInfo['lead'];
    //         $business = $lead->hasBusiness;
    //         $fullName = ($user->first_name ?? 'N/A') . ' ' . ($user->last_name ?? 'N/A');
    //         $data[] = [
    //             'user_id' => $user->id ?? 'N/A',
    //             'first_name' => $user->first_name ?? 'N/A',
    //             'last_name' => $user->last_name ?? 'N/A',
    //             'email' => $user->email ?? 'N/A',
    //             'phone_number' => $user->phone_number ?? 'N/A',
    //             'distance' => round($distanceInfo['distance'], 2),
    //             'lead_id' => $lead->id ?? "N/A",
    //             'visit_date' => $lead->visit_date ?? "N/A",
    //             'business_id' => $business->id ?? 'N/A',
    //             'Name' => $business->name ?? 'N/A',
    //             'lead_first_name' => $business->owner_first_name ?? 'N/A',
    //             'lead_last_name' => $business->owner_last_name ?? 'N/A',
    //             'lead_email' => $business->owner_email ?? 'N/A',
    //             'lead_number' => $business->owner_number ?? 'N/A',
    //             'pincode' => $business->pincode ?? 'N/A',
    //         ];
    //     }

    //     return response()->json([
    //         'code' => 200,
    //         'data' => $data,
    //         'message' => 'Completed leads retrieved and sorted by distance successfully'
    //     ]);
    //      if($leads->count()){
    //         return response()->json([
    //             'code'=>200,
    //             'data'=>$leads,
    //             'message'=>'leads retrive successfully'
    //         ]);
    //     } else {
    //         return response()->json([
    //             'code'=>200,
    //             'data'=>[
    //                 'leads'=>[]
    //             ],
    //             'message'=>'leads not found'
    //         ]);
    //     }

    // }
  
    
    public function getDoneLeads(Request $request){
        $user = Auth::guard('api')->user();
        $userLatitude = $user->latitude;
        $userLongitude = $user->longitude;
    
        $teamId = $user->id;
    
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        $lastWeekStart = Carbon::now()->subWeek()->startOfWeek();
        $lastWeekEnd = Carbon::now()->subWeek()->endOfWeek();
    
        $dateFilter = $request->input('date_filter', 'all'); // 'today', 'yesterday', 'both', 'last_week', or 'all'
    
        // Get count of all done leads for the user's team
        $totalDoneLeadsCount = Leads::where('team_id', $teamId)
                                     ->where('leads.ti_status', 1)
                                     ->count();
    
        // Filter leads based on the date filter
        $leads = Leads::with('hasBusiness')
                        ->where('team_id', $teamId)
                        ->where('leads.ti_status', 1)
                        ->when($dateFilter === 'today', function ($query) use ($today) {
                            return $query->whereDate('created_at', $today);
                        })
                        ->when($dateFilter === 'yesterday', function ($query) use ($yesterday) {
                            return $query->whereDate('created_at', $yesterday);
                        })
                        ->when($dateFilter === 'both', function ($query) use ($today, $yesterday) {
                            return $query->whereDate('created_at', $today)
                                         ->orWhereDate('created_at', $yesterday);
                        })
                        ->when($dateFilter === 'last_week', function ($query) use ($lastWeekStart, $lastWeekEnd) {
                            return $query->whereBetween('created_at', [$lastWeekStart, $lastWeekEnd]);
                        })
                        ->get();
    
        $leadDistances = [];
    
        foreach ($leads as $lead) {
            $distance = $this->haversineGreatCircleDistance(
                $userLatitude,
                $userLongitude,
                $lead->hasBusiness->latitude,
                $lead->hasBusiness->longitude
            );
    
            $leadDistances[] = [
                'lead' => $lead,
                'distance' => $distance
            ];
        }
    
        usort($leadDistances, function ($a, $b) {
            return $a['distance'] <=> $b['distance'];
        });
    
        $data = [];
    
        foreach ($leadDistances as $distanceInfo) {
            $lead = $distanceInfo['lead'];
            $business = $lead->hasBusiness;
            $fullName = ($user->first_name ?? 'N/A') . ' ' . ($user->last_name ?? 'N/A');
    
            // Determine if the lead is from today, yesterday, or last week
            $createdAt = Carbon::parse($lead->created_at);
            $leadDate = $createdAt->isToday() ? 'today' :
                        ($createdAt->isYesterday() ? 'yesterday' :
                        ($createdAt->between($lastWeekStart, $lastWeekEnd) ? 'last_week' : 'other'));
    
            $data[] = [
                'user_id' => $user->id ?? 'N/A',
                'first_name' => $user->first_name ?? 'N/A',
                'last_name' => $user->last_name ?? 'N/A',
                'email' => $user->email ?? 'N/A',
                'phone_number' => $user->phone_number ?? 'N/A',
                'distance' => round($distanceInfo['distance'], 2),
                'lead_id' => $lead->id ?? "N/A",
                'visit_date' => $lead->visit_date ?? "N/A",
                'business_id' => $business->id ?? 'N/A',
                'Name' => $business->name ?? 'N/A',
                'lead_first_name' => $business->owner_first_name ?? 'N/A',
                'lead_last_name' => $business->owner_last_name ?? 'N/A',
                'lead_email' => $business->owner_email ?? 'N/A',
                'lead_number' => $business->owner_number ?? 'N/A',
                'pincode' => $business->pincode ?? 'N/A',
                'lead_date' => $leadDate // New field indicating 'today', 'yesterday', 'last_week', or 'other'
            ];
        }
    
        return response()->json([
            'code' => 200,
            'data' => $data,
            'date_filter' => $dateFilter,
            'total_done_leads_count' => $totalDoneLeadsCount, // Total count of all done leads
            'message' => 'Completed leads retrieved and sorted by distance successfully'
        ]);
        if($leads->count()){
                    return response()->json([
                        'code'=>200,
                        'data'=>$leads,
                        'message'=>'leads retrive successfully'
                    ]);
                } else {
                    return response()->json([
                        'code'=>200,
                        'data'=>[
                            'leads'=>[]
                        ],
                        'message'=>'leads not found'
                    ]);
                }
    }
    
    // public function todayLeads(){
    //     try {
    //     $user = Auth::guard('api')->user();
        // $leads = Leads::with('hasBusiness')
        // ->where('team_id',$user->id)->where('leads.ti_status',0)
        // ->whereDate('updated_at',Carbon::today())->paginate(5);
    //     if($leads->count()){
    //         return response()->json([
    //             'code'=>200,
    //             'data'=>$leads,
    //             'message'=>'leads retrive successfully'
    //         ]);
    //     } else {
    //         return response()->json([
    //             'code'=>200,
    //             'data'=>[
    //                 'leads'=>[]
    //             ],
    //             'message'=>'leads not found'
    //         ]);
    //     }
    //     } catch (\Throwable $th) {
    //         return response()->json([
    //             'code'=>$th->getCode(),
    //             'data'=>[],
    //             'message'=>$th->getMessage()
    //         ]);
    //     }
    // }
 
public function todayLeads(Request $request)
{
    try {
        $user = User::find(Auth::guard('api')->user()->id);
        $userLatitude = $user->latitude;
        $userLongitude = $user->longitude;

        // Fetch leads created today
        $leads = Leads::whereDate('created_at', today())->get();

        $leadDistances = [];

        foreach ($leads as $lead) {
            $distance = $this->haversineGreatCircleDistance(
                $userLatitude,
                $userLongitude,
                $lead->business->latitude,
                $lead->business->longitude
            );

            $leadDistances[] = [
                'lead' => $lead,
                'distance' => $distance
            ];
        }

        usort($leadDistances, function ($a, $b) {
            return $a['distance'] <=> $b['distance'];
        });

        $data = [];

        foreach ($leadDistances as $distanceInfo) {
            $lead = $distanceInfo['lead'];
            $business = $lead->business;
            $fullName = ($user->first_name ?? 'N/A') . ' ' . ($user->last_name ?? 'N/A');
            $data[] = [
                'user_id' => $user->id ?? 'N/A',
                'first_name' => $user->first_name ?? 'N/A',
                'last_name' => $user->last_name ?? 'N/A',
                'email' => $user->email ?? 'N/A',
                'phone_number' => $user->phone_number ?? 'N/A',
                'distance' => round($distanceInfo['distance'], 2),
                'lead_id'=>$lead->id ?? "N/A",
                'visit_date'=>$lead->visit_date ?? "N/A",
                'business_id' => $business->id ?? 'N/A',
                'Name' => $business->name ?? 'N/A',
                'lead_first_name' => $business->owner_first_name ?? 'N/A',
                'lead_last_name' => $business->owner_last_name ?? 'N/A',
                'lead_email' => $business->owner_email ?? 'N/A',
                'lead_number' => $business->owner_number ?? 'N/A',
                'pincode' => $business->pincode ?? 'N/A',
            ];
        }

        return response()->json([
            'code' => 200,
            'data' => $data,
            'message' => 'Leads retrieved and sorted by distance successfully'
        ]);
    } catch (\Throwable $th) {
        return response()->json([
            'code' => $th->getCode(),
            'data' => [],
            'message' => $th->getMessage()
        ]);
    }
}


private function haversineGreatCircleDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371)
{
    $latFrom = deg2rad($latitudeFrom);
    $lonFrom = deg2rad($longitudeFrom);
    $latTo = deg2rad($latitudeTo);
    $lonTo = deg2rad($longitudeTo);

    $latDelta = $latTo - $latFrom;
    $lonDelta = $lonTo - $lonFrom;

    $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
      cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
    return $angle * $earthRadius;
}

    

    public function leadReport(){
        try {
            $status = request()->has('status') &&  request()->get('status') == "completed" ? 1: 0;
            $startDate = Carbon::now()->startOfMonth(); // You can set the start date as needed
            $endDate = Carbon::now()->endOfMonth(); // Current date
            $records = Leads::selectRaw('DATE(updated_at) as date, COUNT(*) as count')
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->where('ti_status',$status)
            ->groupBy('date')
            ->get();

            $dateRange = collect(Carbon::parse($startDate)->daysUntil($endDate)->toArray());
            $chartData = $dateRange->map(function ($date) use ($records) {
                $record = $records->firstWhere('date', $date->toDateString());

                return [
                    'date' => $date->toDateString(),
                    'count' => $record ? $record->count : 0,
                ];
            });
            return response()->json([
                'code'=>200,
                'message'=>'Chart Data found',
                'data' => $chartData
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'code'=>$th->getCode(),
                'message'=>$th->getMessage(),
                'data' => []
            ]);
        }

    }
  
  
    public function createLead(){
        try {
        //    dd(request()->all());
       
           $validator = Validator::make(request()->all(),[
            'owner_first_name'=>'required|max:25',
            'owner_last_name'=>'required|max:25',
            'owner_number'=>'required|digits:10',
            'owner_email'=>'required|email',
            'company_name'=>'required|max:50',
            'country'=>'required|max:25',
            'state'=>'required|max:25',
            'city'=>'required|max:25',
            'area'=>'required|max:25',
            'pincode'=>'required|digits:6',
            'latitude'=>'required|max:10',
            'longitude'=>'required|max:10'
           ]);
           if($validator->fails()){
                return response()->json([
                    'code'=>401,
                    'data'=>[],
                    'message'=>$validator->errors()->first()
                ],401);
           }
           $leadData = [
            'name'=>request()->get('company_name'),
            'owner_first_name'=>request()->get('owner_first_name'),
            'owner_last_name'=>request()->get('owner_last_name'),
            'owner_number'=>request()->get('owner_number'),
            'owner_email'=>request()->get('owner_email'),
            'ti_status'=>0,
            'pincode'=>request()->get('pincode'),
            'city'=>request()->get('city'),
            'state'=>request()->get('state'),
            'country'=>request()->get('country'),
            'area'=>request()->get('area'),
            'latitude'=>request()->get('latitude'),
            'longitude'=>request()->get('longitude')
           ];
           $business = Business::create($leadData);
        //    $asignLeadData  = [
        //     'business_id'=>$business->id,
        //     'team_id'=>Auth::user()->id,
        //     'ti_status'=>0,
        //     'location',
        //     'remark',
        //     'selfie',

        //    ];
        //    $assignLead = Leads::create($asignLeadData);
        //    $data = $assignLead->toArray();
        //    $data['hasBusiness'] = $assignLead->getBusiness()->toArray();
        //    dd($business);

          // notifications 
        $message = ' Lead Created Successfully, contact admin to approve and assign';

          $data = [
            'message' =>$message,
            'user_name' =>$business->name ,  // Ensure there's a space between first name and last name
        ];
        $notification = new NewLeadNotification($data);
        $users = User::all(); // Assuming you want to notify all users
        Notification::send($users, $notification);

       
           return response()->json([
                'code'=>200,
                'data'=>$business,
                'mesage'=>"Lead Created Successfully, contact admin to approve and assing"
           ],200);

         
        } catch (\Throwable $th) {
            return response()->json([
                'code'=>$th->getCode(),
                'data'=>[],
                'message'=>$th->getMessage()
            ],500);
        }
    }

    // public function updateLead($id){
    //     try {
    //         $validator = Validator::make(request()->all(),[
    //             'remarks'=>'|max:100',
    //             'selfie'=>'required|mimes:png,jpg',
    //             'latitude'=>'required',
    //             'longitude'=>'required'
    //         ]);
    //         if($validator->fails()){
    //             return response()->json([
    //                 'code'=>401,
    //                 'data'=>[],
    //                 'message'=>$validator->errors()->first()
    //             ],401);
    //         }
    //         $lead = Leads::find($id);
    //         if($lead){
    //             $lead->remark = request()->get('remarks');
    //             $lead->putFile('selfie',request()->file('selfie'),'');
    //             $lead->latitude = request()->get('latitude');
    //             $lead->longitude = request()->get('longitude');
    //             $lead->ti_status = request()->get('status');
    //             $lead->save();
    //             $leadData= $lead->toArray();
    //             $leadData['selfie']=$lead->getSelfieUrl();
    //             $leadData['hasBusiness']= $lead->getBusiness();

             
    //             // notifications
    //             $message = $lead->business->name .' Lead  successfully';
    //             $data = [
    //               'message' =>$message,
    //               'user_name' =>$lead->user->first_name. ' ' .$lead->user->last_name . ' Has compleated' ,  // Ensure there's a space between first name and last name
    //           ];
    //           $notification = new NewLeadNotification($data);
    //           $users = User::all(); // Assuming you want to notify all users
    //           Notification::send($users, $notification);
    //         //   dd($leadData);
    //             return response()->json([
    //                 'code'=>200,
    //                 'data'=>$leadData,
    //                 'message'=>"Lead updated successfuly"
    //             ],200);
    //         } else {
    //             return response()->json([
    //                 'code'=>404,
    //                 'data'=>[],
    //                 'message'=>"Lead Not found"
    //             ],404);
    //         }
    //     } catch (\Throwable $th) {
    //         return response()->json([
    //             'code'=>$th->getCode(),
    //             'data'=>[],
    //             'message'=>$th->getMessage()
    //         ],500);
    //     }
    // }

     public function updateLead($id){
        try {
            $validator = Validator::make(request()->all(),[
                'remarks'=>'|max:100',
                'selfie'=>'required|mimes:png,jpg',
                'latitude'=>'required',
                'longitude'=>'required',
                // lead 
                'name'=>'required',
                'owner_first_name'=>'required',
                'owner_last_name'=>'required',
                'owner_number'=>'required|digits:10',
                'owner_email'=>'required|email',
                'country'=>'required',
                'state'=>'required',
                'city'=>'required',
                'area'=>'required',
                'pincode'=>'required|digits:6',
            ]);
            if($validator->fails()){
                return response()->json([
                    'code'=>401,
                    'data'=>[],
                    'message'=>$validator->errors()->first()
                ],401);
            }
            $lead = Leads::find($id);
            $business = Business::find($id);
            if($lead){
                $business->name = request()->get('name');
                $business->owner_first_name = request()->get('owner_first_name');
                $business->owner_last_name = request()->get('owner_last_name');
                $business->owner_email = request()->get('owner_email');
                $business->owner_number = request()->get('owner_number');
                $business->country = request()->get('country');
                $business->state = request()->get('state');
                $business->city = request()->get('city');
                $business->area = request()->get('area');
                $business->pincode = request()->get('pincode');

                $lead->remark = request()->get('remarks');
                $lead->putFile('selfie',request()->file('selfie'),'');
                $lead->latitude = request()->get('latitude');
                $lead->longitude = request()->get('longitude');

                $lead->ti_status = request()->get('status');
                $lead->save();
                $business->save();
                $leadData= $lead->toArray();
                $leadData['selfie']=$lead->getSelfieUrl();
                // $leadData['hasBusiness']= $lead->getBusiness();

             
                // notifications
                $message = $lead->business->name .' Lead  successfully';
                $data = [
                  'message' =>$message,
                  'user_name' =>$lead->user->first_name. ' ' .$lead->user->last_name . ' Has compleated' ,  // Ensure there's a space between first name and last name
              ];
              $notification = new NewLeadNotification($data);
              $users = User::all(); // Assuming you want to notify all users
              Notification::send($users, $notification);
                return response()->json([
                    'code'=>200,
                    'data'=>$leadData,$business,
                    'message'=>"Lead updated successfuly"
                ],200);
            } else {
                return response()->json([
                    'code'=>404,
                    'data'=>[],
                    'message'=>"Lead Not found"
                ],404);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'code'=>$th->getCode(),
                'data'=>[],
                'message'=>$th->getMessage()
            ],500);
        }
    }

    public function detailLeadView($id){
        try {
            $lead = Leads::with('hasBusiness')->find($id);
            if($lead){
                return response()->json([
                    'code'=>200,
                    'data'=>$lead,
                    'message'=>"Lead Found"
                ],500);
            } else {
                return response()->json([
                    'code'=>404,
                    'data'=>[],
                    'message'=>"Lead Not found"
                ],404);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'code'=>$th->getCode(),
                'data'=>[],
                'message'=>$th->getMessage()
            ],500);
        }
    }
}
