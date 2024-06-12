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

    public function getPendingLeads(Request $request)
    {
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
            ->whereIn('leads.ti_status', [0, 2])
            ->count();

        // Get count of today's pending leads
        $todayPendingLeadsCount = Leads::where('team_id', $teamId)
            ->whereIn('leads.ti_status', [0, 2])
            ->whereDate('visit_date', $today)
            ->count();

        // Get count of yesterday's pending leads
        $yesterdayPendingLeadsCount = Leads::where('team_id', $teamId)
            ->whereIn('leads.ti_status', [0, 2])
            ->whereDate('visit_date', $yesterday)
            ->count();

        // Get count of last week's pending leads
        $lastWeekPendingLeadsCount = Leads::where('team_id', $teamId)
            ->whereIn('leads.ti_status', [0, 2])
            ->whereBetween('visit_date', [$lastWeekStart, $lastWeekEnd])
            ->count();


        // new 
        // Get count of all done leads for the user's team
        $totalDoneLeadsCount = Leads::where('team_id', $teamId)
            ->where('leads.ti_status', 1)
            ->count();

        // Get count of all pending leads for the user's team


        // Get count of today's done leads
        $todayDoneLeadsCount = Leads::where('team_id', $teamId)
            ->where('leads.ti_status', 1)
            ->whereDate('visit_date', $today)
            ->count();

        // Get count of yesterday's done leads
        $yesterdayDoneLeadsCount = Leads::where('team_id', $teamId)
            ->where('leads.ti_status', 1)
            ->whereDate('visit_date', $yesterday)
            ->count();

        // Get count of last week's done leads
        $lastWeekDoneLeadsCount = Leads::where('team_id', $teamId)
            ->where('leads.ti_status', 1)
            ->whereBetween('visit_date', [$lastWeekStart, $lastWeekEnd])
            ->count();
        // Filter leads based on the date filter
        $leadsPending = Leads::with('hasBusiness')
            ->where('team_id', $teamId)
            ->whereIn('leads.ti_status', [0, 2])
            ->when($dateFilter === 'today', function ($query) use ($today) {
                return $query->whereDate('visit_date', $today);
            })
            ->when($dateFilter === 'yesterday', function ($query) use ($yesterday) {
                return $query->whereDate('visit_date', $yesterday);
            })
            ->when($dateFilter === 'both', function ($query) use ($today, $yesterday) {
                return $query->whereDate('visit_date', $today)
                    ->orWhereDate('visit_date', $yesterday);
            })
            ->when($dateFilter === 'last_week', function ($query) use ($lastWeekStart, $lastWeekEnd) {
                return $query->whereBetween('visit_date', [$lastWeekStart, $lastWeekEnd]);
            })
            ->get();

        $leadDistances = [];

        foreach ($leadsPending as $lead) {
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

        $Pending_leads_data = [];

        foreach ($leadDistances as $distanceInfo) {
            $lead = $distanceInfo['lead'];
            $business = $lead->hasBusiness;
            $fullName = ($user->first_name ?? 'N/A') . ' ' . ($user->last_name ?? 'N/A');

            // Determine if the lead is from today, yesterday, or last week
            $createdAt = Carbon::parse($lead->created_at);
            $leadDate = $createdAt->isToday() ? 'today' : ($createdAt->isYesterday() ? 'yesterday' : ($createdAt->between($lastWeekStart, $lastWeekEnd) ? 'last_week' : 'other'));

            $Pending_leads_data[] = [
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
                'lead_latitude' => $business->latitude,
                'lead_longitude' => $business->longitude,
                'lead_date' => $leadDate // New field indicating 'today', 'yesterday', 'last_week', or 'other'
            ];
        }

        return response()->json([
            'code' => 200,
            'data' => $Pending_leads_data,
            'date_filter' => $dateFilter,
            'lead_latitude' => $business->latitude,
            'lead_longitude' => $business->longitude,
            'total_done_leads_count' => $totalDoneLeadsCount, // Total count of all done leads
            'total_pending_leads_count' => $totalPendingLeadsCount, // Total count of all pending leads
            'today_done_leads_count' => $todayDoneLeadsCount, // Count of today's done leads
            'yesterday_done_leads_count' => $yesterdayDoneLeadsCount, // Count of yesterday's done leads
            'last_week_done_leads_count' => $lastWeekDoneLeadsCount, // Count of last week's done leads
            'today_pending_leads_count' => $todayPendingLeadsCount, // Count of today's pending leads
            'yesterday_pending_leads_count' => $yesterdayPendingLeadsCount, // Count of yesterday's pending leads
            'last_week_pending_leads_count' => $lastWeekPendingLeadsCount, // Count of last week's pending leads
            'message' => 'Pending leads retrieved and sorted by distance successfully'
        ]);
    }


    public function getDoneLeads(Request $request)
    {
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

        // Get count of all pending leads for the user's team
        $totalPendingLeadsCount = Leads::where('team_id', $teamId)
            ->whereIn('leads.ti_status', [0, 2])
            ->count();

        // Get count of today's done leads
        $todayDoneLeadsCount = Leads::where('team_id', $teamId)
            ->where('leads.ti_status', 1)
            ->whereDate('visit_date', $today)
            ->count();

        // Get count of yesterday's done leads
        $yesterdayDoneLeadsCount = Leads::where('team_id', $teamId)
            ->where('leads.ti_status', 1)
            ->whereDate('visit_date', $yesterday)
            ->count();

        // Get count of last week's done leads
        $lastWeekDoneLeadsCount = Leads::where('team_id', $teamId)
            ->where('leads.ti_status', 1)
            ->whereBetween('visit_date', [$lastWeekStart, $lastWeekEnd])
            ->count();

        // Get count of today's pending leads
        $todayPendingLeadsCount = Leads::where('team_id', $teamId)
            ->whereIn('leads.ti_status', [0, 2])
            ->whereDate('visit_date', $today)
            ->count();

        // Get count of yesterday's pending leads
        $yesterdayPendingLeadsCount = Leads::where('team_id', $teamId)
            ->whereIn('leads.ti_status', [0, 2])
            ->whereDate('visit_date', $yesterday)
            ->count();

        // Get count of last week's pending leads
        $lastWeekPendingLeadsCount = Leads::where('team_id', $teamId)
            ->whereIn('leads.ti_status', [0, 2])
            ->whereBetween('visit_date', [$lastWeekStart, $lastWeekEnd])
            ->count();

        // Filter leads based on the date filter
        $leadsCompleted = Leads::with('hasBusiness')
            ->where('team_id', $teamId)
            ->where('leads.ti_status', 1)
            ->when($dateFilter === 'today', function ($query) use ($today) {
                return $query->whereDate('visit_date', $today);
            })
            ->when($dateFilter === 'yesterday', function ($query) use ($yesterday) {
                return $query->whereDate('visit_date', $yesterday);
            })
            ->when($dateFilter === 'both', function ($query) use ($today, $yesterday) {
                return $query->whereDate('visit_date', $today)
                    ->orWhereDate('visit_date', $yesterday);
            })
            ->when($dateFilter === 'last_week', function ($query) use ($lastWeekStart, $lastWeekEnd) {
                return $query->whereBetween('visit_date', [$lastWeekStart, $lastWeekEnd]);
            })
            ->get();

        $leadDistances = [];

        foreach ($leadsCompleted as $lead) {
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

        $completed_leads_data = [];

        foreach ($leadDistances as $distanceInfo) {
            $lead = $distanceInfo['lead'];
            $business = $lead->hasBusiness;
            $fullName = ($user->first_name ?? 'N/A') . ' ' . ($user->last_name ?? 'N/A');

            // Determine if the lead is from today, yesterday, or last week
            $createdAt = Carbon::parse($lead->created_at);
            $leadDate = $createdAt->isToday() ? 'today' : ($createdAt->isYesterday() ? 'yesterday' : ($createdAt->between($lastWeekStart, $lastWeekEnd) ? 'last_week' : 'other'));

            $completed_leads_data[] = [
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
                'lead_latitude' => $business->latitude,
                'lead_longitude' => $business->longitude,
                'lead_date' => $leadDate // New field indicating 'today', 'yesterday', 'last_week', or 'other'
            ];
        }


        return response()->json([
            'code' => 200,
            'data' => $completed_leads_data,
            'date_filter' => $dateFilter,
            'total_done_leads_count' => $totalDoneLeadsCount, // Total count of all done leads
            'total_pending_leads_count' => $totalPendingLeadsCount, // Total count of all pending leads
            'today_done_leads_count' => $todayDoneLeadsCount, // Count of today's done leads
            'yesterday_done_leads_count' => $yesterdayDoneLeadsCount, // Count of yesterday's done leads
            'last_week_done_leads_count' => $lastWeekDoneLeadsCount, // Count of last week's done leads
            'today_pending_leads_count' => $todayPendingLeadsCount, // Count of today's pending leads
            'yesterday_pending_leads_count' => $yesterdayPendingLeadsCount, // Count of yesterday's pending leads
            'last_week_pending_leads_count' => $lastWeekPendingLeadsCount, // Count of last week's pending leads
            'message' => 'Completed leads retrieved and sorted by distance successfully'
        ]);
    }



    public function todayLeads(Request $request)
    {
        try {
            // Get the authenticated user
            $user = User::find(Auth::guard('api')->user()->id);


            $userLatitude = $user->latitude;
            $userLongitude = $user->longitude;

            // Set the timezone explicitly
            $timeZone = config('app.timezone');
            $currentDate = Carbon::now();

            // Fetch leads created today and belonging to the authenticated user
            $leads = Leads::where('team_id', $user->id)
                ->whereDate('created_at', $currentDate)
                ->get();

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

            // Sort leads by distance
            usort($leadDistances, function ($a, $b) {
                return $a['distance'] <=> $b['distance'];
            });

            $data = [];

            foreach ($leadDistances as $distanceInfo) {
                $lead = $distanceInfo['lead'];
                $business = $lead->business;
                $data[] = [
                    'user_id' => $user->id ?? 'N/A',
                    'first_name' => $user->first_name ?? 'N/A',
                    'last_name' => $user->last_name ?? 'N/A',
                    'email' => $user->email ?? 'N/A',
                    'phone_number' => $user->phone_number ?? 'N/A',
                    'distance' => round($distanceInfo['distance'], 2),
                    'lead_id' => $lead->id ?? 'N/A',
                    'visit_date' => $lead->visit_date ?? 'N/A',
                    'business_id' => $business->id ?? 'N/A',
                    'Name' => $business->name ?? 'N/A',
                    'lead_first_name' => $business->owner_first_name ?? 'N/A',
                    'lead_last_name' => $business->owner_last_name ?? 'N/A',
                    'lead_email' => $business->owner_email ?? 'N/A',
                    'lead_number' => $business->owner_number ?? 'N/A',
                    'pincode' => $business->pincode ?? 'N/A',
                    'lead_latitude' => $business->latitude ?? 'N/A',
                    'lead_longitude' => $business->longitude ?? 'N/A',
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



    // public function leadReport(){
    //     try {
    //         $status = request()->has('status') &&  request()->get('status') == "completed" ? 1: 0;
    //         $startDate = Carbon::now()->startOfMonth(); // You can set the start date as needed
    //         $endDate = Carbon::now()->endOfMonth(); // Current date
    //         $records = Leads::selectRaw('DATE(updated_at) as date, COUNT(*) as count')
    //         ->whereBetween('updated_at', [$startDate, $endDate])
    //         ->where('ti_status',$status)
    //         ->groupBy('date')
    //         ->get();

    //         $dateRange = collect(Carbon::parse($startDate)->daysUntil($endDate)->toArray());
    //         $chartData = $dateRange->map(function ($date) use ($records) {
    //             $record = $records->firstWhere('date', $date->toDateString());

    //             return [
    //                 'date' => $date->toDateString(),
    //                 'count' => $record ? $record->count : 0,
    //             ];
    //         });
    //         return response()->json([
    //             'code'=>200,
    //             'message'=>'Chart Data found',
    //             'data' => $chartData
    //         ]);
    //     } catch (\Throwable $th) {
    //         return response()->json([
    //             'code'=>$th->getCode(),
    //             'message'=>$th->getMessage(),
    //             'data' => []
    //         ]);
    //     }

    // }


    public function leadReport()
    {
        try {
            $user = User::find(Auth::guard('api')->user()->id);

            // Check if status parameter is provided
            $statusProvided = request()->has('status');
            $status = request()->get('status');

            // Calculate the start and end dates for each week
            $currentWeekStart = Carbon::now()->startOfWeek();
            $currentWeekEnd = Carbon::now();

            $lastWeek1Start = $currentWeekStart->copy()->subWeek();
            $lastWeek1End = $currentWeekStart->copy()->subSecond();

            $lastWeek2Start = $lastWeek1Start->copy()->subWeek();
            $lastWeek2End = $lastWeek1Start->copy()->subSecond();

            $lastWeek3Start = $lastWeek2Start->copy()->subWeek();
            $lastWeek3End = $lastWeek2Start->copy()->subSecond();

            // Build the query based on status and user
            $query = Leads::selectRaw('DATE(created_at) as date')
                ->selectRaw('SUM(CASE WHEN ti_status = 2 THEN 1 ELSE 0 END) AS pending_count')
                ->selectRaw('SUM(CASE WHEN ti_status = 1 THEN 1 ELSE 0 END) AS completed_count')
                ->whereBetween('created_at', [$lastWeek3Start, $currentWeekEnd])
                ->where('team_id', $user->id) // Add condition for logged-in user
                ->groupBy('date'); // Group by date

            // If status is provided, filter by status
            if ($statusProvided) {
                if ($status == "pending") {
                    $query->where('ti_status', 2);
                } elseif ($status == "completed") {
                    $query->where('ti_status', 1);
                }
            }

            $records = $query->get();

            // Helper function to get counts for a specific week
            $getCountsForWeek = function ($start, $end) use ($records) {
                $dateRange = collect(Carbon::parse($start)->daysUntil($end)->toArray());
                return $dateRange->map(function ($date) use ($records) {
                    $record = $records->firstWhere('date', $date->toDateString());
                    return [
                        'date' => $date->toDateString(),
                        'day' => $date->format('l'), // Get the day of the week
                        'pending_count' => $record ? $record->pending_count : 0,
                        'completed_count' => $record ? $record->completed_count : 0,
                    ];
                });
            };

            // Get counts for each week
            $data = [
                'last_week_3' => $getCountsForWeek($lastWeek3Start, $lastWeek3End),
                'last_week_2' => $getCountsForWeek($lastWeek2Start, $lastWeek2End),
                'last_week_1' => $getCountsForWeek($lastWeek1Start, $lastWeek1End),
                'current_week' => $getCountsForWeek($currentWeekStart, $currentWeekEnd),
            ];

            return response()->json([
                'code' => 200,
                'message' => 'Chart Data found successfully',
                'data' => $data
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'code' => $th->getCode(),
                'message' => $th->getMessage(),
                'data' => []
            ]);
        }
    }

    public function createLead()
    {
        try {
            //    dd(request()->all());

            $validator = Validator::make(request()->all(), [
                'owner_first_name' => 'required|max:25',
                'owner_last_name' => 'required|max:25',
                'owner_number' => 'required|digits:10',
                'owner_email' => 'required|email',
                'company_name' => 'required|max:50',
                'country' => 'required|max:25',
                'state' => 'required|max:25',
                'city' => 'required|max:25',
                'area' => 'required|max:25',
                'pincode' => 'required|digits:6',
                'latitude' => 'nullable|max:10',
                'longitude' => 'nullable|max:10'
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'code' => 401,
                    'data' => [],
                    'message' => $validator->errors()->first()
                ], 401);
            }
            $leadData = [
                'name' => request()->get('company_name'),
                'owner_first_name' => request()->get('owner_first_name'),
                'owner_last_name' => request()->get('owner_last_name'),
                'owner_number' => request()->get('owner_number'),
                'owner_email' => request()->get('owner_email'),
                'ti_status' => 0,
                'pincode' => request()->get('pincode'),
                'city' => request()->get('city'),
                'state' => request()->get('state'),
                'country' => request()->get('country'),
                'area' => request()->get('area'),
                'latitude' => request()->get('latitude') ?? 0,  // Default value
                'longitude' => request()->get('longitude') ?? 0,  // Default value
            ];
            $business = Business::create($leadData);

            $users = User::find(Auth::guard('api')->user()->id);

            $data = [
                'message' => 'A new business ' . $business->name . ' has been created.',
                'user_name' => 'Hey ' . $users->first_name . ' ' . $users->last_name . ' !',  // Ensure there's a space between first name and last name
            ];
            $notification = new NewLeadNotification($data);
            Notification::send($users, $notification);


            return response()->json([
                'code' => 200,
                'data' => $business,
                'mesage' => "Lead Created Successfully, contact admin to approve and assing"
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'code' => $th->getCode(),
                'data' => [],
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function updateLead($id)
    {
        try {
            $validator = Validator::make(request()->all(), [
                'remarks' => '|max:100',
                'selfie' => 'required|mimes:png,jpg',
                'latitude' => 'required',
                'longitude' => 'required',
                // lead 
                'name' => 'required',
                'owner_first_name' => 'required',
                'owner_last_name' => 'required',
                'owner_number' => 'required|digits:10',
                'owner_email' => 'required|email',
                'country' => 'required',
                'state' => 'required',
                'city' => 'required',
                'area' => 'required',
                'pincode' => 'required|digits:6',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'code' => 401,
                    'data' => [],
                    'message' => $validator->errors()->first()
                ], 401);
            }
            $lead = Leads::find($id);
            $business = Business::find($id);
            if ($lead) {
                $business = $lead->business; // Get the associated business object

                if ($business) { // Check if the business object exists
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
                    $business->save();

                    $lead->remark = request()->get('remarks');
                    $lead->putFile('selfie', request()->file('selfie'), '');
                    $lead->latitude = request()->get('latitude');
                    $lead->longitude = request()->get('longitude');
                    $lead->ti_status = request()->get('status');
                    $lead->save();

                    $leadData = $lead->toArray();
                    $leadData['selfie'] = $lead->getSelfieUrl();

                    // notifications
                    // 4.The lead, "Lead Name" has been successfully updated.

                    $message = $business->name . ' Lead  successfully updated'; // Update the message to indicate lead update
                    $data = [
                        'message' => $message,
                        'user_name' => 'Hey ' . $lead->user->first_name . ' ' . $lead->user->last_name . ' !',  // Ensure there's a space between first name and last name
                    ];
                    $lead->user->notify(new NewLeadNotification($data));

                    return response()->json([
                        'code' => 200,
                        'data' => $leadData,
                        'business' => $business, // Return the updated business data
                        'message' => "Lead updated successfully"
                    ], 200);
                } else {
                    return response()->json([
                        'code' => 404,
                        'data' => [],
                        'message' => "Business associated with the lead not found"
                    ], 404);
                }
            } else {
                return response()->json([
                    'code' => 404,
                    'data' => [],
                    'message' => "Lead not found"
                ], 404);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'code' => $th->getCode(),
                'data' => [],
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function detailLeadView($id)
    {
        try {
            $lead = Leads::with('hasBusiness')->find($id);
            if ($lead) {
                return response()->json([
                    'code' => 200,
                    'data' => $lead,
                    'message' => "Lead Found"
                ], 500);
            } else {
                return response()->json([
                    'code' => 404,
                    'data' => [],
                    'message' => "Lead Not found"
                ], 404);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'code' => $th->getCode(),
                'data' => [],
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
