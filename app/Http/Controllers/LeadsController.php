<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Business;
use App\Helpers\DataTableHelper;
use App\Models\Leads;
use App\Models\User;
use App\Events\LeadAssigned;
use Illuminate\Support\Facades\Auth;
use App\Helpers\DistanceHelper;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Notifications\NewBusinessNotification;
use Illuminate\Support\Facades\Notification;
use Carbon\Carbon;
use App\Models\Location;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Validator;
use App\Notifications\NewLeadNotification;
use App\Services\GeocodingService;

class LeadsController extends Controller
{
    use \App\Traits\TraitController;

    protected $geocodingService;

    public function __construct(GeocodingService $geocodingService)
    {
        $this->geocodingService = $geocodingService;
    }

    public function index()
    {
        return view('leads.index', [
            'title' => 'Leads',
            'urlListData' => routePut('leads.loadlist'), 'table' => 'tableLeads'
        ]);
    }


    //     public function todayVisit(Request $request)
    // {
    //     try {
    //         // Fetch today's date
    //         $today = now()->toDateString();

    //         // Fetch the member ID from the request
    //         $memberId = $request->input('member_id');

    //         $leadsQuery = Leads::whereIn('ti_status', [1, 2, 3, 4, 5])
    //             ->where('visit_date', $today);

    //         // Apply search criteria
    //         if ($srch = DataTableHelper::search()) {
    //             $leadsQuery->where(function ($query) use ($srch) {
    //                 foreach (['name', 'owner_first_name', 'owner_last_name', 'owner_email', 'owner_number', 'pincode', 'city', 'state', 'country', 'area'] as $k => $v) {
    //                     if (!$k) $query->where($v, 'like', '%' . $srch . '%');
    //                     else $query->orWhere($v, 'like', '%' . $srch . '%');
    //                 }
    //             });
    //         }

    //         // Filter leads by member ID if provided
    //         if ($memberId) {
    //             $leadsQuery->where('user_id', $memberId);
    //         }

    //         // Get the filtered leads
    //         $leads = $leadsQuery->get();

    //         // Process the leads data
    //         $data = [];
    //         // Iterate over each lead to process and collect the required information
    //         foreach ($leads as $lead) {
    //             if ($lead->user && $lead->business) {
    //                 // Calculate the distance between user and business
    //                 $distance = $this->haversineGreatCircleDistance(
    //                     $lead->user->latitude,
    //                     $lead->user->longitude,
    //                     $lead->business->latitude,
    //                     $lead->business->longitude
    //                 );

    //                 $data[] = [
    //                     'first_name' => $lead->user->first_name,
    //                     'last_name' => $lead->user->last_name,
    //                     'phone_number' => $lead->user->phone_number,
    //                     'distance' => round($distance, 2),
    //                     'name' => $lead->business->name,
    //                     'ti_status' => $lead->leadStatus(),
    //                     'lead_first_name' => $lead->business->owner_first_name,
    //                     'lead_last_name' => $lead->business->owner_last_name,
    //                     'lead_email' => $lead->business->owner_email,
    //                     'lead_number' => $lead->business->owner_number,
    //                     'details' => '<a href="' . route('teams.detail', ['id' => $lead->id]) . '">View Details</a>'
    //                 ];
    //             }
    //         }

    //         // Sort the data by distance in ascending order
    //         usort($data, fn($a, $b) => $a['distance'] <=> $b['distance']);

    //         // Get the count of the data
    //         $count = count($data);

    //         // Return the JSON response with the collected data
    //         return response()->json([
    //             'draw' => intval($request->input('draw')),
    //             'recordsTotal' => $count,
    //             'recordsFiltered' => $count,
    //             'data' => $data,
    //             'leads' => $leads, // Pass the leads collection
    //         ]);
    //     } catch (\Throwable $th) {
    //         // Return error message if any exception occurs
    //         return response()->json(['error' => $th->getMessage()], 500);
    //     }
    // }
    // public function todayVisit(Request $request, $export = false)
    // {
    //     try {
    //         $today = now()->toDateString();
    //         $memberId = $request->input('member_id', 'all');
    //         $searchValue = $request->input('search.value');

    //         $leadsQuery = Leads::select([
    //             'business.name as business_name',
    //             'business.owner_full_name',
    //             'business.owner_number',
    //             'leads.id as lead_id',
    //             'leads.ti_status as ti_status',
    //             'users.first_name as user_first_name',
    //             'users.last_name as user_last_name',
    //             'users.phone_number as user_phone_number'
    //         ])
    //             ->leftJoin('business', 'leads.business_id', '=', 'business.id')
    //             ->leftJoin('users', 'leads.team_id', '=', 'users.id')
    //             ->whereIn('leads.ti_status', [1, 2, 3, 4, 5, 0])
    //             ->where('leads.visit_date', $today);

    //         // Handle search
    //         if (!empty($searchValue)) {
    //             $leadsQuery->where(function ($query) use ($searchValue) {
    //                 $query->where('business.name', 'like', '%' . $searchValue . '%')
    //                     ->orWhere('business.owner_full_name', 'like', '%' . $searchValue . '%')
    //                     ->orWhere('users.first_name', 'like', '%' . $searchValue . '%')
    //                     ->orWhere('business.owner_number', 'like', '%' . $searchValue . '%')
    //                     ->orWhere('users.last_name', 'like', '%' . $searchValue . '%')
    //                     ->orWhere('business.city', 'like', '%' . $searchValue . '%')
    //                     ->orWhere('business.state', 'like', '%' . $searchValue . '%')
    //                     ->orWhere('business.country', 'like', '%' . $searchValue . '%')
    //                     ->orWhere('business.area', 'like', '%' . $searchValue . '%');
    //             });
    //         }

    //         if ($memberId && $memberId !== 'all') {
    //             $leadsQuery->where('leads.team_id', $memberId);
    //         }

    //         $leads = $leadsQuery->get();
    //         $data = [];

    //         foreach ($leads as $lead) {
    //             $distance = $this->haversineGreatCircleDistance(
    //                 $lead->latitude, // Assuming these are correct fields in your tables
    //                 $lead->longitude,
    //                 $lead->business_latitude,
    //                 $lead->business_longitude
    //             );

    //             $data[] = [
    //                 'first_name' => $lead->user_first_name,
    //                 'last_name' => $lead->user_last_name,
    //                 'phone_number' => $lead->user_phone_number,
    //                 'distance' => round($distance, 2),
    //                 'name' => $lead->business_name,
    //                 'ti_status' => $lead->leadStatus(),
    //                 'lead_full_name' => $lead->owner_full_name,
    //                 'lead_number' => $lead->owner_number,
    //                 'details' => '<a href="' . route('teams.detail', ['id' => $lead->lead_id]) . '">View Details</a>'
    //             ];
    //         }

    //         usort($data, fn ($a, $b) => $a['distance'] <=> $b['distance']);
    //         $count = count($data);
    //         if ($export) {
    //             return $this->exportToExcel($data);
    //         }
    //         return response()->json([
    //             'draw' => intval($request->input('draw')),
    //             'recordsTotal' => $count,
    //             'recordsFiltered' => $count,
    //             'data' => $data
    //         ]);
    //     } catch (\Throwable $th) {
    //         return response()->json(['error' => $th->getMessage()], 500);
    //     }
    // }
    public function todayVisit(Request $request, $export = false)
    {
        try {
            $today = now()->toDateString(); // Get today's date in 'Y-m-d' format
            $memberId = $request->input('member_id', 'all');
            $searchValue = $request->input('search.value');

            $teamMembers = User::whereDoesntHave('roles')
                ->orWhereHas('roles', function ($query) {
                    $query->where('name', 'user');
                })
                ->get();

            $leadsQuery = Leads::select([
                'business.name as business_name',
                'business.owner_full_name',
                'business.owner_number',
                'leads.id as lead_id',
                'leads.ti_status as ti_status',
                'users.first_name as user_first_name',
                'users.last_name as user_last_name',
                'users.phone_number as user_phone_number',
                'leads.visit_date',
                'business.latitude as business_latitude',
                'business.longitude as business_longitude'
            ])
                ->leftJoin('business', 'leads.business_id', '=', 'business.id')
                ->leftJoin('users', 'leads.team_id', '=', 'users.id')
                ->whereIn('leads.ti_status', [1, 2, 3, 4, 5, 0])
                ->whereDate('leads.created_at', $today) // Filter by today's date
                ->orderBy('leads.created_at', 'desc');

            if (!empty($searchValue)) {
                $leadsQuery->where(function ($query) use ($searchValue) {
                    $query->where('business.name', 'like', '%' . $searchValue . '%')
                        ->orWhere('business.owner_full_name', 'like', '%' . $searchValue . '%')
                        ->orWhere('users.first_name', 'like', '%' . $searchValue . '%')
                        ->orWhere('business.owner_number', 'like', '%' . $searchValue . '%')
                        ->orWhere('users.last_name', 'like', '%' . $searchValue . '%')
                        ->orWhere('business.city', 'like', '%' . $searchValue . '%')
                        ->orWhere('business.state', 'like', '%' . $searchValue . '%')
                        ->orWhere('business.country', 'like', '%' . $searchValue . '%')
                        ->orWhere('business.area', 'like', '%' . $searchValue . '%');
                });
            }

            if ($memberId && $memberId !== 'all') {
                $leadsQuery->where('leads.team_id', $memberId);
            }

            $leads = $leadsQuery->get();

            foreach ($leads as $lead) {
                if ($lead->business) {
                    $lead->distance = $this->haversineGreatCircleDistance(
                        $lead->business_latitude,
                        $lead->business_longitude,
                        $lead->latitude,  // Ensure you have latitude and longitude for the other point (user or another location)
                        $lead->longitude
                    );
                    $lead->distance = round($lead->distance, 2);
                } else {
                    $lead->distance = null;
                }
            }

            if ($export) {
                return $this->exportToExcel($leads);
            }

            return response()->json(['leads' => $leads]);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }


    /**
     * Calculate the great-circle distance between two points on the Earth's surface.
     * @param float $latitudeFrom Latitude of point A
     * @param float $longitudeFrom Longitude of point A
     * @param float $latitudeTo Latitude of point B
     * @param float $longitudeTo Longitude of point B
     * @param float $earthRadius Radius of the Earth (defaults to 6371.0 km)
     * @return float Distance between the two points in kilometers
     */
    private function haversineGreatCircleDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371.0)
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





    public function view($id)
    {
        $useID = useId($id);
        $heading = "View Details";
        $business  = Business::find($useID);
        return view('leads.view', ['heading' => $heading, 'business' => $business]);
    }

    public function loadList()
    {
        try {
            $q = Business::query();

            // Apply search filter
            if ($srch = DataTableHelper::search()) {
                $q->where(function ($query) use ($srch) {
                    foreach (['name', 'owner_full_name', 'owner_email', 'owner_number', 'pincode', 'city', 'state', 'country', 'area', 'address'] as $v) {
                        $query->orWhere($v, 'like', '%' . $srch . '%');
                    }
                });
            }

            // Count total records
            $count = $q->count();


            // Apply pagination
            $q->skip(DataTableHelper::start())->limit(DataTableHelper::limit());

            // Fetch data
            $businesses = $q->get();

            $data = [];
            foreach ($businesses as $business) {
                $assignedUser = 'N/A';
                $lead = $business->leads()->first();
                if ($lead) {
                    $assignedUser = $lead->user ? $lead->user->first_name : 'N/A';
                    $assignedUser .= $lead->user && $lead->user->last_name ? ' ' . $lead->user->last_name : '';
                }
                $data[] = [
                    'name' => putNA($business->name),
                    'owner_name' => putNA($business->owner_full_name),
                    'owner_number' => putNA($business->owner_number),
                    'city' => putNA($business->city),
                    'ti_status' => $business->mapStatus(),
                    'assigned_user_name' => $assignedUser, // New column for assigned user's name
                    'details' => '<a href="' . route('leads.view', ['id' => encrypt($business->getId())]) . '">View Details</a>',
                    'actions' => putNA(DataTableHelper::listActions([
                        'edit' => routePut('leads.edit', ['id' => encrypt($business->getId())])
                    ]))
                ];
            }

            return response()->json([
                'draw' => request('draw'),
                'recordsTotal' => $count,
                'recordsFiltered' => $count,
                'data' => $data
            ]);
        } catch (\Throwable $th) {
            return response()->json(['error' => exMessage($th)], 500);
        }
    }


    public function create($id = 0)
    {
        $useID = $id ? useId($id) : 0;
        $title = $useID ? "Edit Lead" : "Create Lead";
        $heading = $useID ? "Edit" : "Create";
        $business  = Business::find($useID);
        $business = $business ?? new Business();
        return view('leads.form', ['heading' => $heading, 'title' => $title, 'business' => $business]);
    }

    // public function save()
    // {
    //     try {
    //         $rules = [
    //             'name' => 'required',
    //             'owner_full_name' => 'required',
    //             'owner_number' => 'required|digits:10',
    //             'owner_email' => 'required|email',
    //             'country' => 'required',
    //             'state' => 'required',
    //             'city' => 'required',
    //             'area' => 'required',
    //             'Address' => 'required',
    //             'pincode' => 'required|digits:6',

    //         ];

    //         $messages = [
    //             'name.required' => "Please Provide Business Name",
    //             'owner_full_name.required' => "Please Provide Owner Name",
    //             'owner_email.required' => "Please Provide Owner Email",
    //             'owner_email.email' => "please provide valid email",
    //             'owner_number.required' => "Please provide Owner phone number",
    //             'owner_number.digits' => "phone number should be exact :digits number",
    //             'country.required' => "Please provide Country",
    //             'state.required' => "Please provide state",
    //             'city.required' => "Please provide city",
    //             'area.required' => "Please provide area",
    //             'Address.required' => "Please provide Address",
    //             'pincode.required' => "please provide pincode",
    //             'pincode.digits' => "pincode number should be exact :digits number",

    //         ];

    //         $validator = validate(request()->all(), $rules, $messages);

    //         if (!empty($validator)) {
    //             return $this->resp(0, $validator[0], [], 500);
    //         }

    //         $id = useId(request()->get('id'));
    //         $isNewBusiness = !$id;
    //         $business = $isNewBusiness ? new Business() : Business::find($id);

    //         if ($isNewBusiness) {
    //             // Check if email or mobile number already exists
    //             $existingBusinessemail = Business::where('owner_email', request()->get('owner_email'))
    //                 ->first();
    //             $existingBusinessmobile = Business::Where('owner_number', request()->get('owner_number'))
    //                 ->first();
    //             if ($existingBusinessemail) {
    //                 return $this->resp(0, 'Email is already exist in our system . please provide new email', [], 500);
    //             } elseif ($existingBusinessmobile) {
    //                 return $this->resp(0, 'Mobile Number is already exist in our system . please provide new Mobile Number', [], 500);
    //             }
    //         }

    //         $status = $isNewBusiness ? 0 : $business->ti_status;

    //         $address = request()->get('Address') . ', ' . request()->get('area') . ', ' . request()->get('city')
    //                      . ', ' . request()->get('state') . ', ' . request()->get('country') . ', ' 
    //                      . request()->get('pincode');
    //         \Log::info('Geocoding address: ' . $address);
    //         $coordinates = $this->geocodingService->getCoordinates($address);

    //         if (!$coordinates) {
    //             return $this->resp(0, 'Unable to get coordinates for the given address.', [], 500);
    //         }

    //         $business->name = request()->get('name');
    //         $business->owner_full_name = request()->get('owner_full_name');
    //         $business->owner_email = request()->get('owner_email');
    //         $business->owner_number = request()->get('owner_number');
    //         $business->country = request()->get('country');
    //         $business->state = request()->get('state');
    //         $business->city = request()->get('city');
    //         $business->area = request()->get('area');
    //         $business->address = request()->get('Address');
    //         $business->pincode = request()->get('pincode');
    //         // $business->latitude = request()->get('latitude');
    //         // $business->longitude = request()->get('longitude');
    //         $business->latitude = $coordinates['latitude'];
    //         $business->longitude = $coordinates['longitude'];
    //         $business->ti_status = $status;
    //         $business->save();

    //         $messages = $isNewBusiness ? "Lead created successfully" : "Lead updated successfully";
    //         $url = routePut('leads.list');

    //         // Notify all users
    //         Notification::send(User::all(), new NewBusinessNotification($business, $isNewBusiness));

    //         return $this->resp(1, $messages, ['url' => $url], 200);
    //     } catch (\Throwable $th) {
    //         return $this->resp(0, $th->getMessage(), [], 500);
    //     }
    // }
    public function save()
    {
        try {
            $rules = [
                'name' => 'required',
                'owner_full_name' => 'required',
                'owner_number' => 'required|digits:10',
                'owner_email' => 'required|email',
                'country' => 'required',
                'state' => 'required',
                'city' => 'required',
                'area' => 'required',
                'Address' => 'required',
                'pincode' => 'required|digits:6',
            ];

            $messages = [
                'name.required' => "Please Provide Business Name",
                'owner_full_name.required' => "Please Provide Owner Name",
                'owner_email.required' => "Please Provide Owner Email",
                'owner_email.email' => "Please provide a valid email",
                'owner_number.required' => "Please provide Owner phone number",
                'owner_number.digits' => "Phone number should be exactly :digits digits",
                'country.required' => "Please provide Country",
                'state.required' => "Please provide State",
                'city.required' => "Please provide City",
                'area.required' => "Please provide Area",
                'Address.required' => "Please provide Address",
                'pincode.required' => "Please provide Pincode",
                'pincode.digits' => "Pincode should be exactly :digits digits",
            ];

            $validator = validate(request()->all(), $rules, $messages);

            if (!empty($validator)) {
                return $this->resp(0, $validator[0], [], 500);
            }

            $id = useId(request()->get('id'));
            $isNewBusiness = !$id;
            $business = $isNewBusiness ? new Business() : Business::find($id);

            if ($isNewBusiness) {
                // Check if email or mobile number already exists
                $existingBusinessemail = Business::where('owner_email', request()->get('owner_email'))
                    ->first();
                $existingBusinessmobile = Business::Where('owner_number', request()->get('owner_number'))
                    ->first();
                if ($existingBusinessemail) {
                    return $this->resp(0, 'Email is already exist in our system . please provide new email', [], 500);
                } elseif ($existingBusinessmobile) {
                    return $this->resp(0, 'Mobile Number is already exist in our system . please provide new Mobile Number', [], 500);
                }
            }

            $status = $isNewBusiness ? 0 : $business->ti_status;

            // Get address components from the request
            $country = request()->get('country');
            $state = request()->get('state');
            $city = request()->get('city');
            $pincode = request()->get('pincode');
            $area = request()->get('area');
            $address = request()->get('Address');

            // Log address components for debugging
            \Log::info('Geocoding address components: Country=' . $country . ', State=' . $state . ', City=' . $city . ', Pincode=' . $pincode . ', Area=' . $area . ', Address=' . $address);

            // Get coordinates using GeocodingService
            $coordinates = $this->geocodingService->getCoordinates($country, $state, $city, $pincode, $area, $address);

            if (!$coordinates) {
                \Log::error('Unable to get coordinates for the given address components.');
                return $this->resp(0, 'Unable to get coordinates for the given address.', [], 500);
            }

            // Populate business model with data
            $business->name = request()->get('name');
            $business->owner_full_name = request()->get('owner_full_name');
            $business->owner_email = request()->get('owner_email');
            $business->owner_number = request()->get('owner_number');
            $business->country = $country;
            $business->state = $state;
            $business->city = $city;
            $business->area = $area;
            $business->address = $address;
            $business->pincode = $pincode;
            $business->latitude = $coordinates['latitude'];
            $business->longitude = $coordinates['longitude'];
            $business->ti_status = $status;
            $business->save();
            $messages = $isNewBusiness ? "Lead created successfully" : "Lead updated successfully";
            $url = routePut('leads.list');

            // Notify all users
            Notification::send(User::all(), new NewBusinessNotification($business, $isNewBusiness));

            return $this->resp(1, $messages, ['url' => $url], 200);
        } catch (\Throwable $th) {
            return $this->resp(0, $th->getMessage(), [], 500);
        }
    }







    public function asignLead()
    {
        $leadsObj = Business::where('ti_status', [0, 1, 2, 3, 4])->get();
        $leads = [];
        // dd($leadsObj);
        foreach ($leadsObj as $key => $value) {
            $leads[$value->id] = $value->owner_full_name . "  -  " . $value->area;
        }
        // .$value->owner_first_name." " .$value->owner_last_name."-" 
        $rawUsers = User::whereDoesntHave('roles')->get();
        $users = [];
        foreach ($rawUsers as $value) {
            $users[$value->id] = $value->first_name . " " . $value->last_name;
        }
        // Notify users
        $assigningUser = Auth::user(); // Assuming the current logged-in user is assigning the lead

        return view('leads.asign', ['title' => 'Asign Lead', 'user' => $users, 'leads' => $leads]);
    }

    public function leadAsign()
    {
        try {
            $lead = new Leads();
            $business = Business::find(request()->get('business_id'));
            $user = User::find(request()->get('user_ids'));
            $lead->business_id = $business->id;
            $lead->team_id = $user->id;
            $lead->ti_status = 2;
            $lead->visit_date = \Carbon\Carbon::today();
            $lead->save();
            $business->ti_status = 5;
            $business->save();
            // Notify the assigned user
            $assigningUser = Auth::user(); // Assuming the current logged-in user is assigning the lead
            $user->notify(new NewLeadNotification([
                'message' => ' A new lead, ' . $business->owner_full_name  . ' has been assigned to you.',
                'user_name' => 'Hey ' . $assigningUser->first_name . ' ' . $assigningUser->last_name . '!',
            ]));
            return $this->resp(1, "Lead Asign Successfuly", ['url' => routePut('teams.view')], 200);
        } catch (\Throwable $th) {
            return $this->resp(0, $th->getMessage(), [], 500);
        }
    }

    public function bulkupload()
    {
        $rules = [
            'file' => 'required'
        ];

        // Validate the file
        $validator = Validator::make(request()->all(), $rules);
        if ($validator->fails()) {
            return $this->resp(0, 'Invalid file format.', [], 400);
        }

        try {
            $file = request()->file('file');
            $spreadsheet = IOFactory::load($file->getPathname());
            $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

            // Extract headings from the first row
            $headings = array_map('trim', array_shift($sheetData));
            $data = [];

            foreach ($sheetData as $rowData) {
                // Combine headings with data to create associative array
                $data[] = array_combine($headings, $rowData);
            }

            foreach ($data as $value) {
                // Find the nearest user based on latitude and longitude
                $nearestUser = $this->findNearestUser($value['latitude'], $value['longitude']);

                if ($nearestUser) {
                    $filledData = [
                        'name' => $value['Company name'],
                        'owner_full_name' => $value['Owner Full name'],
                        'owner_number' => $value['Contact'],
                        'owner_email' => $value['email'],
                        'pincode' => $value['Pincode'],
                        'city' => $value['City'],
                        'state' => $value['State'],
                        'area' => $value['Area'],
                        'address' => $value['Address'],
                        'created_at' => now(),
                        'updated_at' => now(),
                        'team_id' => $nearestUser->id, // Assign the nearest user ID
                        'visit_date' => now()
                    ];
                    $status = $filledData ? $business->ti_status : 0;
                    // Create the business entry
                    $business = Business::create($filledData);

                    // Assign user based on pincode
                    $userId = User::getUserIdUsingPincode($business->pincode);
                    if ($userId) {
                        $leadData = [
                            'business_id' => $business->id,
                            'team_id' => $userId,
                            'visit_date' => now()
                        ];
                        Leads::create($leadData);
                        $business->ti_status = 5;
                        $business->save();
                    }
                }
            }

            return $this->resp(1, "Upload successful");
        } catch (\Throwable $e) {
            return $this->resp(0, exMessage($e), [], 500);
        }
    }

    /**
     * Find the nearest user based on latitude and longitude.
     */
    private function findNearestUser($latitude, $longitude)
    {
        return User::selectRaw("*, (6371 * acos(cos(radians($latitude)) * cos(radians(latitude)) * cos(radians($longitude) - radians(longitude)) + sin(radians($latitude)) * sin(radians(latitude)))) AS distance")
            ->orderBy('distance')
            ->first();
    }

    /**
     * Assign the nearest team member based on latitude and longitude.
     */
    private function assignTeamMember($latitude, $longitude)
    {
        return User::where('latitude', $latitude)
            ->where('longitude', $longitude)
            ->first();
    }



    // public function getLeadsByStatus($status){
    //         if($status == "pending"){
    //             $title = "Pending Visits";
    //             return view('dashboard.leads',['title'=>$title,'table'=>'tblLeadStatus','urlListData'=>routePut('leads.getleadList',['status'=>$status])]);
    //         } elseif($status == "completed"){
    //             $title = "Completed Visits";
    //             return view('dashboard.leads',['title'=>$title,'table'=>'tblLeadStatus','urlListData'=>routePut('leads.getleadList',['status'=>$status])]);
    //         } elseif($status == "total") {
    //             $title = "Total Visits";
    //             return view('dashboard.leads',['title'=>$title,'table'=>'tblLeadStatus','urlListData'=>routePut('leads.getleadList',['status'=>$status])]);
    //         } else {
    //             abort(404);
    //         }
    // }

    // public function loadLeadsByStatus($status) {
    //     try {
    //         $q = Business::query();
    //         $q = $q->leftJoin('leads', 'leads.business_id', '=', 'business.id')
    //                ->leftJoin('users', 'leads.team_id', '=', 'users.id') // Corrected table alias 'users'
    //                ->select([
    //                    'business.name as business_name',
    //                    'business.owner_first_name',
    //                    'business.owner_last_name',
    //                    'business.owner_email',
    //                    'business.owner_number',
    //                    'leads.id as lead_id',
    //                    'leads.ti_status as ti_status',
    //                    'users.first_name as user_first_name', // Select user details
    //                    'users.last_name as user_last_name',
    //                    'users.phone_number as user_phone_number'
    //                ]);

    //         switch ($status) {
    //             case "pending":
    //                 $q->where('leads.ti_status', 2);
    //                 break;
    //             case "completed":
    //                 $q->where('leads.ti_status', 1);
    //                 break;

    //             case "total":
    //                 // No additional conditions for total
    //                 break;
    //         }

    //         if ($srch = DataTableHelper::search()) {
    //             $q = $q->where(function ($query) use ($srch) {
    //                 foreach (['business.name', 'business.owner_first_name', 'business.owner_last_name', 'business.owner_email', 'business.owner_number', 'business.pincode', 'business.city', 'business.state', 'business.country', 'business.area'] as $k => $v) {
    //                     if (!$k) $query->where($v, 'like', '%' . $srch . '%');
    //                     else $query->orWhere($v, 'like', '%' . $srch . '%');
    //                 }
    //             });
    //         }

    //         $count = $q->count();

    //         if (DataTableHelper::sortBy() == 'ti_status') {
    //             $q = $q->orderBy(DataTableHelper::sortBy(), DataTableHelper::sortDir() == 'asc' ? 'desc' : 'asc');
    //         } else {
    //             $q = $q->orderBy(DataTableHelper::sortBy(), DataTableHelper::sortDir());
    //         }

    //         $q = $q->skip(DataTableHelper::start())->limit(DataTableHelper::limit());


    //         $teamMembers = User::all();

    //         $data = [];
    //         foreach ($q->get() as $single) {
    //             $user_full_name = ($single->user_first_name && $single->user_last_name)
    //             ? $single->user_first_name . ' ' . $single->user_last_name
    //             : 'N/A (Please assign a lead to Team)';
    //             $details_link = $single->lead_id 
    //             ? '<a href="' . route('teams.detail', ['id' => $single->lead_id]) . '">View Details</a>' 
    //             : 'N/A';
    //             $ti_status = $this->mapTiStatus($single->ti_status);
    //             $data[] = [
    //                 'name' => putNA($single->business_name),
    //                 'owner_full_name' => putNA($single->owner_first_name . ' ' . $single->owner_last_name),

    //                 'owner_email' => putNA($single->owner_email),
    //                 'owner_number' => putNA($single->owner_number),
    //                 'ti_status' => $ti_status,
    //                 'user_full_name' =>$user_full_name,
    //                 'details' => $details_link
    //             ];
    //         }
    //         dd($teamMembers);
    //         return $this->resp(1, '', [
    //             'draw' => request('draw'),
    //             'recordsTotal' => $count,
    //             'recordsFiltered' => $count,
    //             'data' => $data,
    //             'teamMembers'=>$teamMembers
    //         ]);

    //     } catch (\Throwable $th) {
    //         return $this->resp(0, exMessage($th), [], 500);
    //     }

    // }

    // Controller code
    public function getLeadsByStatus($status)
    {
        switch ($status) {
            case "pending":
                $title = "Pending Visits";
                break;
            case "completed":
                $title = "Completed Visits";
                break;
            case "total":
                $title = "Total Visits";
                break;
            default:
                abort(404);
        }
        $teamMembers = User::whereDoesntHave('roles')
            ->orWhereHas('roles', function ($query) {
                $query->where('name', 'user');
            })
            ->get();

        return view('dashboard.leads', [
            'title' => $title,
            'table' => 'tblLeadStatus',
            'teamMembers' => $teamMembers,
            'urlListData' => routePut('leads.getleadList', ['status' => $status])
        ]);
    }

    public function loadLeadsByStatus($status, $export = false)
    {
        try {
            $q = Business::query();
            $q = $q->leftJoin('leads', 'leads.business_id', '=', 'business.id')
                ->leftJoin('users', 'leads.team_id', '=', 'users.id')
                ->select([
                    'business.name as business_name',
                    'business.owner_full_name',
                    'business.owner_email',
                    'business.owner_number',
                    'leads.id as lead_id',
                    'leads.ti_status as ti_status',
                    'users.first_name as user_first_name',
                    'users.last_name as user_last_name',
                    'users.phone_number as user_phone_number'
                ])
                ->orderBy('visit_date', 'desc');

            switch ($status) {
                case "pending":
                    $q->where('leads.ti_status', '!=', 1);
                    break;
                case "completed":
                    $q->where('leads.ti_status', 1);
                    break;
                    // No additional conditions for "total"
            }

            // Handle search
            if ($srch = DataTableHelper::search()) {
                $q = $q->where(function ($query) use ($srch) {
                    foreach ([
                        'business.name', 'business.owner_full_name', 'business.address',
                        'users.first_name', 'business.owner_number', 'users.last_name', 'business.city', 'business.state', 'business.country', 'business.area'
                    ] as $v) {
                        $query->orWhere($v, 'like', '%' . $srch . '%');
                    }
                });
            }

            // Handle team member filter
            $memberId = request()->input('member_id');
            if ($memberId) {
                $q->where('leads.team_id', $memberId);
            }

            $count = $q->count();
            $q = $q->skip(DataTableHelper::start())->limit(DataTableHelper::limit());

            $teamMembers = User::whereDoesntHave('roles')
                ->orWhereHas('roles', function ($query) {
                    $query->where('name', 'user');
                })
                ->get();

            $data = [];
            foreach ($q->get() as $single) {
                $userFullName = $single->user_first_name && $single->user_last_name ? $single->user_first_name . ' ' . $single->user_last_name : 'N/A (Please assign a lead to Team)';
                $detailsLink = $single->lead_id ? '<a href="' . route('teams.detail', ['id' => $single->lead_id]) . '">View Details</a>' : 'N/A';
                $tiStatus = $this->mapTiStatus($single->ti_status);
                $data[] = [
                    'name' => putNA($single->business_name),
                    'owner_full_name' => putNA($single->owner_full_name),
                    'owner_number' => putNA($single->owner_number),
                    'ti_status' => $tiStatus,
                    'user_full_name' => $userFullName,
                    'details' => $detailsLink
                ];
            }

            if ($export) {
                return $this->exportToExcel($data);
            }

            return $this->resp(1, '', [
                'draw' => request('draw'),
                'recordsTotal' => $count,
                'recordsFiltered' => $count,
                'data' => $data,
                'teamMembers' => $teamMembers
            ]);
        } catch (\Throwable $th) {
            return $this->resp(0, exMessage($th), [], 500);
        }
    }
    public function exportLeads(Request $request)
    {
        $status = $request->input('status');
        $memberId = $request->input('member_id');

        return $this->loadLeadsByStatus($status, true, $memberId);
    }
    protected function exportToExcel($data)
    {
        if (empty($data)) {
            return response()->json(['message' => 'No data available to export.'], 400);
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set header
        $headers = ['Business Name', 'Owner Full Name', 'Owner Number', 'TI Status', 'User Full Name', 'Details'];
        $sheet->fromArray($headers, NULL, 'A1');

        // Fill data
        $sheet->fromArray($data, NULL, 'A2');

        // Create the writer
        $writer = new Xlsx($spreadsheet);
        $filename = 'leads_export_' . date('YmdHis') . '.xlsx';
        $filePath = storage_path('exports/' . $filename);

        // Ensure the directory exists
        $directory = dirname($filePath);
        if (!file_exists($directory)) {
            mkdir($directory, 0775, true);
        }

        // Save the file
        $writer->save($filePath);

        // Return the file path to download
        return response()->download($filePath)->deleteFileAfterSend(true);
    }



    public function calculateDistance(Request $request)
    {
        try {
            $q = Business::query();



            $data = [];
            foreach ($q->get() as $single) {
                $assignedUser = 'N/A';
                $lead = $single->leads()->first();
                if ($lead) {
                    $assignedUser = $lead->user ? $lead->user->first_name : 'N/A';
                    $assignedUser .= $lead->user && $lead->user->last_name ? ' ' . $lead->user->last_name : '';
                }
                $data[] = [
                    // 'id' => '<input type="checkbox" class="chk-multi-check" value="' . $single->getId() . '" />',
                    'name' => putNA($single->name),
                    'owner_name' => putNA($single->owner_first_name . ' ' . $single->owner_last_name),
                    'owner_number' => putNA($single->owner_number),
                    'city' => putNA($single->city),
                    'ti_status' => $single->leadStatus(),
                    'assigned_user_name' => $assignedUser, // New column for assigned user's name
                    'details' => '<a href="' . route('leads.view', ['id' => encrypt($single->getId())]) . '">
                    View Details</a>',
                    'actions' => putNA(DataTableHelper::listActions([
                        'edit' => routePut('leads.edit', ['id' => encrypt($single->getId())])
                    ]))
                ];
            }
            dd($data);
            return $this->resp(1, '', [
                'draw' => request('draw'),
                'recordsTotal' => $count,
                'recordsFiltered' => $count,
                'data' => $data
            ]);
        } catch (\Throwable $th) {
            return $this->resp(0, exMessage($th), [], 500);
        }
    }

    // private function haversineGreatCircleDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371)
    // {
    //     $latFrom = deg2rad($latitudeFrom);
    //     $lonFrom = deg2rad($longitudeFrom);
    //     $latTo = deg2rad($latitudeTo);
    //     $lonTo = deg2rad($longitudeTo);

    //     $latDelta = $latTo - $latFrom;
    //     $lonDelta = $lonTo - $lonFrom;

    //     $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
    //         cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
    //     return $angle * $earthRadius;
    // }

    private function mapTiStatus($status)
    {
        switch ($status) {
            case 1:
                return '<span class="badge badge-success">Completed</span>';
            case 2:
                return '<span class="badge badge-warning">Pending</span>';
            case 3:
                return '<span class="badge badge-warning">Pending</span>';
            case 4:
                return '<span class="badge badge-danger">Reject</span>';
            case 5:
                return '<span class="badge badge-secondary">Assigned</span>';
            case 0:
                return '<span class="badge badge-warning">Pending</span>';
            default:
                return '<span class="badge badge-dark">Unassigned</span>';
        }
    }
}
