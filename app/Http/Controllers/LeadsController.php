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
use App\Notifications\NewLeadNotification;

class LeadsController extends Controller
{
    use \App\Traits\TraitController;
    public function index(){
        return view('leads.index',[
            'title'=>'Leads',
            'urlListData'=>routePut('leads.loadlist'),'table' => 'tableLeads'
        ]);
    }
    

    public function todayVisit(Request $request)
    {
        try {
            // Fetch today's date
            $today = now()->toDateString();
            $leads = Leads::whereIn('ti_status', [0,1,2])
            ->where('visit_date',$today)
            ->get();

            // dd($leads);
          // Apply search criteria
            if ($srch = DataTableHelper::search()) {
                $q = $leads->where(function ($query) use ($srch) {
                    foreach (['name', 'owner_first_name', 'owner_last_name', 'owner_email', 'owner_number', 'pincode', 'city', 'state', 'country', 'area'] as $k => $v) {
                        if (!$k) $query->where($v, 'like', '%' . $srch . '%');
                        else $query->orWhere($v, 'like', '%' . $srch . '%');
                    }
                });
            }
          
    
          
            // Initialize the data array
            $data = [];
    
            // Iterate over each lead to process and collect the required information
            foreach ($leads as $lead) {
                if ($lead->user && $lead->business) {
                    // Calculate the distance between user and business
                    $distance = $this->haversineGreatCircleDistance(
                        $lead->user->latitude,
                        $lead->user->longitude,
                        $lead->business->latitude,
                        $lead->business->longitude
                    );
    
                    // Determine lead status and corresponding button class
                    if ($lead->ti_status == 0) {
                        $lead_status = 'Pending';
                        $button_class = 'btn btn-warning';
                    } elseif ($lead->ti_status == 1) {
                        $lead_status = 'Complete';
                        $button_class = 'btn btn-success';
                    } else {
                        $lead_status = 'Unknown';
                        $button_class = 'btn btn-secondary';
                    }
    
                    // Collect data into the array
                    $data[] = [
                        'first_name'=>$lead->user->first_name,
                        'last_name' =>$lead->user->last_name,
                        'email' => $lead->user->email,
                        'phone_number' => $lead->user->phone_number,
                        'distance' => round($distance, 2),
                        'name' => $lead->business->name,
                        'ti_status' => "<button class=\"$button_class\">$lead_status</button>",
                        'lead_first_name' => $lead->business->owner_first_name,
                        'lead_last_name' => $lead->business->owner_last_name,
                        'lead_email' => $lead->business->owner_email,
                        'lead_number' => $lead->business->owner_number,
                        'pincode' => $lead->business->pincode,
                        'city' => $lead->business->city,
                        'state' => $lead->business->state,
                        'country' => $lead->business->country,
                    ];
                }
            }
    
            // Sort the data by distance in ascending order
            usort($data, fn($a, $b) => $a['distance'] <=> $b['distance']);
    
            // Get the count of the data
            $count = count($data);
            // dd($data);
            // Return the JSON response with the collected data
            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => $count,
                'recordsFiltered' => $count,
                'data' => $data
            ]);
        } catch (\Throwable $th) {
            // Return error message if any exception occurs
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }
    
    // public function todayVisit(){
    //     try {
            
	// 		$q = Business::query();
    //         $q = $q->leftJoin('leads','leads.business_id','business.id');

			
    //         $q->whereDate('leads.visit_date',\Carbon\Carbon::today());
	// 		$count = $q->count();
            
			
	// 		$data = [];
	// 		foreach ($q->get() as $single) {
    //             // $distance = $this->haversineGreatCircleDistance($userLatitude, $userLongitude, $single->latitude, $single->longitude);

	// 			$data[] = [
	// 				// 'id' => '<input type="checkbox" class="chk-multi-check" value="' . $single->getId() . '" />',
	// 				'name' => putNA($single->name),
    //                 'owner_first_name'=>putNA($single->owner_first_name),
    //                 'owner_last_name'=>putNA($single->owner_last_name),
	// 				'owner_email' => putNA($single->owner_email),
    //                 'owner_number'=>putNA($single->owner_number),
    //                 'pincode'=>putNA($single->pincode),
    //                 'city'=>putNA($single->city),
    //                 'state'=>putNA($single->state),
    //                 'country'=>putNA($single->country),
    //                 'area'=>putNA($single->area),
	// 				'ti_status' => $single->leadStatus(),
	// 				'created_at' => putNA($single->showCreated(1)),
	// 				'actions' => putNA(DataTableHelper::listActions([
    //                     'edit'=>routePut('leads.edit',['id'=>encrypt($single->getId())])
    //                 ]))
	// 			];
	// 		}

	// 		return $this->resp(1, '', [
	// 			'draw' => request('draw'),
	// 			'recordsTotal' => $count,
	// 			'recordsFiltered' => $count,
	// 			'data' => $data
	// 		]);
	// 	} catch (\Throwable $th) {
	// 		return $this->resp(0, exMessage($th), [], 500);
	// 	}
    // }
    
    // public function todayVisit()
    // {
    //     try {
    //         // Fetch authenticated user
    //         $user = User::find(Auth::guard('api')->user()->id);
    //         $userLatitude = $user->latitude;
    //         $userLongitude = $user->longitude;
    
    //         // Query to fetch visits for today
    //         $q = Business::query();
    //         $q = $q->leftJoin('leads', 'leads.business_id', 'business.id');
    
    //         // Apply search criteria
    //         if ($srch = DataTableHelper::search()) {
    //             $q = $q->where(function ($query) use ($srch) {
    //                 foreach (['name', 'owner_first_name', 'owner_last_name', 'owner_email', 'owner_number', 'pincode', 'city', 'state', 'country', 'area'] as $k => $v) {
    //                     if (!$k) $query->where($v, 'like', '%' . $srch . '%');
    //                     else $query->orWhere($v, 'like', '%' . $srch . '%');
    //                 }
    //             });
    //         }
    
    //         // Filter visits for today
    //         $q->whereDate('leads.visit_date', \Carbon\Carbon::today());
    
    //         // Count total records
    //         $count = $q->count();
    
    //         // Apply sorting
    //         if (DataTableHelper::sortBy() == 'ti_status') {
    //             $q = $q->orderBy(DataTableHelper::sortBy(), DataTableHelper::sortDir() == 'asc' ? 'desc' : 'asc');
    //         } else {
    //             $q = $q->orderBy(DataTableHelper::sortBy(), DataTableHelper::sortDir());
    //         }
    
    //         // Pagination
    //         $q = $q->skip(DataTableHelper::start())->limit(DataTableHelper::limit());
    
    //         // Fetch data and calculate distance
    //         $data = [];
    //         foreach ($q->get() as $single) {
    //             $distance = $this->haversineGreatCircleDistance(
    //                 $userLatitude,
    //                 $userLongitude,
    //                 $single->latitude,
    //                 $single->longitude
    //             );
    
    //             $data[] = [
    //                 'name' => putNA($single->name),
    //                 'owner_first_name' => putNA($single->owner_first_name),
    //                 'owner_last_name' => putNA($single->owner_last_name),
    //                 'owner_email' => putNA($single->owner_email),
    //                 'owner_number' => putNA($single->owner_number),
    //                 'pincode' => putNA($single->pincode),
    //                 'city' => putNA($single->city),
    //                 'state' => putNA($single->state),
    //                 'country' => putNA($single->country),
    //                 'area' => putNA($single->area),
    //                 'ti_status' => $single->leadStatus(),
    //                 'created_at' => putNA($single->showCreated(1)),
    //                 'distance' => round($distance, 2),
    //                 // 'actions' => putNA(DataTableHelper::listActions([
    //                 //     'edit' => routePut('leads.edit', ['id' => encrypt($single->getId())])
    //                 // ]))
    //             ];
    //         }
    
    //         // Return response
    //         return $this->resp(1, '', [
    //             'draw' => request('draw'),
    //             'recordsTotal' => $count,
    //             'recordsFiltered' => $count,
    //             'data' => $data
    //         ]);
    //     } catch (\Throwable $th) {
    //         return $this->resp(0, exMessage($th), [], 500);
    //     }
    // }
    
   
    public function loadList(){
        try {
			$q = Business::query();
			if ($srch = DataTableHelper::search()) {
				$q = $q->where(function ($query) use ($srch) {
					foreach (['name', 'owner_first_name','owner_last_name', 'owner_email','owner_number','pincode','city','state','country','area'] as $k => $v) {
						if (!$k) $query->where($v, 'like', '%' . $srch . '%');
						else $query->orWhere($v, 'like', '%' . $srch . '%');
					}
				});
			}
			$count = $q->count();

			if (DataTableHelper::sortBy() == 'ti_status') {
				$q = $q->orderBy(DataTableHelper::sortBy(), DataTableHelper::sortDir() == 'asc' ? 'desc' : 'asc');
			} else {
				$q = $q->orderBy(DataTableHelper::sortBy(), DataTableHelper::sortDir());
			}
			$q = $q->skip(DataTableHelper::start())->limit(DataTableHelper::limit());

			$data = [];
			foreach ($q->get() as $single) {
				$data[] = [
					// 'id' => '<input type="checkbox" class="chk-multi-check" value="' . $single->getId() . '" />',
					'name' => putNA($single->name),
                    'owner_first_name'=>putNA($single->owner_first_name),
                    'owner_last_name'=>putNA($single->owner_last_name),
					'owner_email' => putNA($single->owner_email),
                    'owner_number'=>putNA($single->owner_number),
                    'pincode'=>putNA($single->pincode),
                    'city'=>putNA($single->city),
                    'state'=>putNA($single->state),
                    'country'=>putNA($single->country),
                    'area'=>putNA($single->area),
					'ti_status' => $single->leadStatus(),
					'created_at' => putNA($single->showCreated(1)),
					'actions' => putNA(DataTableHelper::listActions([
                        'edit'=>routePut('leads.edit',['id'=>encrypt($single->getId())])
                    ]))
				];
			}

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

    public function create($id = 0){
        $useID = $id ? useId($id) : 0;
        $title = $useID ? "Edit Lead" : "Create Lead";
        $heading = $useID ? "Edit" : "Create";
        $business  = Business::find($useID);
        $business = $business ?? new Business();
        return view('leads.form',['heading'=>$heading,'title'=>$title,'business'=>$business]);
    }
    public function save(){
        try {
            $rules = [
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
                'longitude'=>'required',
                'latitude'=>'required'
            ];
            $messages = [
                'name.required'=>"Please Provide Business Name",
                'owner_first_name.required'=>"Please Provide Owner Name",
                'owner_last_name.required'=>"Please Provide Owner Name",
                'owner_email.required'=>"Please Provide Owner Email",
                'owner_email.email'=>"please provide valid email",
                'owner_number.required'=>"Please provide Owner phone number",
                'owner_number.digits'=>"phone number should be exact :digits number",
                'country.required'=>"Please provide Country",
                'state.required'=>"Please provide state",
                'city.required'=>"Please provide city",
                'area.required'=>"Please provide area",
                'pincode.required'=>"please provide pincode",
                'pincode.digits'=>"pincode number should be exact :digits number",
                'latitude.required'=>"please provide Latitude",
                'longitude.required'=>"please provide Longitude",
            ];
            $validator = validate(request()->all(), $rules, $messages);
            if(!empty($validator)){
                return $this->resp(0,$validator[0],[],500);
            }
            $business = Business::find(useId(request()->get('id')));

            // notification
            $isNewBusiness = !$business;


            $status = $business ? $business->ti_status : 0;
            $business = $business ?? new Business();

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
            $business->latitude = request()->get('latitude');
            $business->longitude = request()->get('longitude');
            $business->ti_status =$status;
            $business->save();
            $messages = useId(request()->get('id')) ?" Lead updated successfuly" :"Lead created successfuly";
            $url = useId(request()->get('id')) ? routePut('leads.list') : routePut('leads.list');

            // Notify all users
            Notification::send(User::all(), new NewBusinessNotification($business, $isNewBusiness));

            return $this->resp(1,$messages,['url'=>$url],200);
        } catch (\Throwable $th) {
            return $this->resp(0,$th->getMessage(),[],500);
        }
    }

    public function asignLead(){
        $leadsObj = Business::where('ti_status',[0,1,2])->get();
        $leads=[];
        // dd($leadsObj);
        foreach ($leadsObj as $key => $value) {
            $leads[$value->id] = $value->owner_first_name. " ".$value->owner_last_name . "  -  " . $value->area;
        }
        // .$value->owner_first_name." " .$value->owner_last_name."-" 
        $rawUsers = User::whereDoesntHave('roles')->get();
        $users = [];
        foreach ($rawUsers as $value) {
            $users[$value->id]=$value->first_name." ".$value->last_name;
        }
         // Notify users
         $assigningUser = Auth::user(); // Assuming the current logged-in user is assigning the lead

        return view('leads.asign',['title'=>'Asign Lead','user'=>$users,'leads'=>$leads]);
    }

    public function leadAsign(){
        try {
            $lead = new Leads();
            $business = Business::find(request()->get('business_id'));
            $user = User::find(request()->get('user_ids'));
            $lead->business_id = $business->id;
            $lead->team_id = $user->id;
            $lead->ti_status = 0;
            $lead->visit_date= \Carbon\Carbon::today();
            $lead->save();
            $business->ti_status = 5;
            $business->save();
            // Notify the assigned user
             $assigningUser = Auth::user(); // Assuming the current logged-in user is assigning the lead
             $user->notify(new NewLeadNotification([
                'message' => ' have been assigned a new lead. lead Name ' . $business->owner_first_name .' ' . $business->owner_last_name,
                'user_name' => $assigningUser->first_name . ' ' . $assigningUser->last_name,
            ]));
            return $this->resp(1,"Lead Asign Successfuly",['url'=>routePut('teams.view')],200);
        } catch (\Throwable $th) {
            return $this->resp(0,$th->getMessage(),[],500);
        }
    }

    public function bulkupload(){
        // dd(request()->all());
        $rules = [
            'file' => 'required|mimes:xlsx,xls'
        ];
        $file = request()->file('file');
        try {
                $spreadsheet = IOFactory::load($file->getPathname());
                $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

                // Extract headings from the first row
                $headings = array_map('trim', array_shift($sheetData));
                $data = [];
                foreach ($sheetData as $rowData) {
                    // Combine headings with data to create associative array
                    $data[] = array_combine($headings, $rowData);

                    // Assuming YourModel has fields corresponding to the headings
                    // YourModel::create($data);
                }
                $filledData = [];
                foreach ($data as $key => $value) {
                    
                    $filledData= [
                        'name'=>$value['Unit name'],
                        'owner_first_name'=>$value['Owner first name'],
                        'owner_last_name'=>$value['Owner last name'],
                        'owner_number'=>$value['Contact'],
                        'owner_email'=>$value['email'],
                        'pincode'=>$value['Pincode'],
                        'city'=>$value['City'],
                        'state'=>$value['State'],
                        'country'=>$value['Unit name'],
                        'latitude'=>$value['latitude'],
                        'longitude'=>$value['longitude'],
                        'area'=>$value['Area'],
                        'address'=>$value['Address'],
                        'created_at'=>\Carbon\Carbon::now(),
                        'updated_at'=>\Carbon\Carbon::now()
                    ];
                    $business = Business::create($filledData);
                    $userId = User::getUserIdUsingPincode($business->pincode);
                    if($userId){
                        $filledLeadData = [
                            'business_id'=>$business->id,
                            'team_id'=>$userId,
                            'visit_date'=>\Carbon\Carbon::today()
                        ];
                        $lead = Leads::create($filledLeadData);
                        $business->ti_status = 5;
                        $business->save();
                    }
                    
                }
            return $this->resp(1,"upload success",$filledData);
        } catch (\Throwable $e) {
            return $this->resp(0, exMessage($e), [], 500);
        }
    }

    public function getLeadsByStatus($status){
            if($status == "pending"){
                $title = "Pending Visits";
                return view('dashboard.leads',['title'=>$title,'table'=>'tblLeadStatus','urlListData'=>routePut('leads.getleadList',['status'=>$status])]);
            } elseif($status == "completed"){
                $title = "Completed Visits";
                return view('dashboard.leads',['title'=>$title,'table'=>'tblLeadStatus','urlListData'=>routePut('leads.getleadList',['status'=>$status])]);
            } elseif($status == "total") {
                $title = "Total Visits";
                return view('dashboard.leads',['title'=>$title,'table'=>'tblLeadStatus','urlListData'=>routePut('leads.getleadList',['status'=>$status])]);
            } else {
                abort(404);
            }
    }
    public function loadLeadsByStatus($status){
        try {
			$q = Business::query();
            $q = $q->leftJoin('leads','leads.business_id','business.id');
            // if($status == "pending"){
            //     $q->where('leads.ti_status',0);
            // } elseif($status == "completed"){
            //     $q->where('leads.ti_status',1);
            // } elseif($status =="total"){
            //     $q->where('leads.ti_status',2);
            // };
            switch ($status) {
                case "pending":
                    $q->where('leads.ti_status', 2);
                    break;
                case "completed":
                    $q->where('leads.ti_status', 1);
                    break;
                case "total":
                    break;
            }
            // if ($status != "total") {
            //     $q->where('leads.visit_date', \Carbon\Carbon::today());
            // } elseif($status !='pending'){
            //     $q->where('leads.visit_date', \Carbon\Carbon::today());
            // }
            // elseif($status !=''){
            //     $q->where('leads.visit_date', \Carbon\Carbon::today());
            // }
            
            // $q->where('leads.visit_date',\Carbon\Carbon::today());

			if ($srch = DataTableHelper::search()) {
				$q = $q->where(function ($query) use ($srch) {
					foreach (['name', 'owner_first_name','owner_last_name', 'owner_email','owner_number','pincode','city','state','country','area'] as $k => $v) {
						if (!$k) $query->where($v, 'like', '%' . $srch . '%');
						else $query->orWhere($v, 'like', '%' . $srch . '%');
					}
				});
			}
			$count = $q->count();

			if (DataTableHelper::sortBy() == 'ti_status') {
				$q = $q->orderBy(DataTableHelper::sortBy(), DataTableHelper::sortDir() == 'asc' ? 'desc' : 'asc');
			} else {
				$q = $q->orderBy(DataTableHelper::sortBy(), DataTableHelper::sortDir());
			}
			$q = $q->skip(DataTableHelper::start())->limit(DataTableHelper::limit());

			$data = [];
			foreach ($q->get() as $single) {
				$data[] = [
					// 'id' => '<input type="checkbox" class="chk-multi-check" value="' . $single->getId() . '" />',
					'name' => putNA($single->name),
                    'owner_first_name'=>putNA($single->owner_first_name),
                    'owner_last_name'=>putNA($single->owner_last_name),
					'owner_email' => putNA($single->owner_email),
                    'owner_number'=>putNA($single->owner_number),
                    'pincode'=>putNA($single->pincode),
                    'city'=>putNA($single->city),
                    'state'=>putNA($single->state),
                    'country'=>putNA($single->country),
                    'area'=>putNA($single->area),
					'ti_status' => $single->leadStatus(),
					'created_at' => putNA($single->showCreated(1)),
					'actions' => putNA(DataTableHelper::listActions([
                        'edit'=>routePut('leads.edit',['id'=>encrypt($single->getId())])
                    ]))
				];
			}
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
  
    public function calculateDistance(Request $request)
    {
        try {
            // Fetch all users from the database
            $users = User::all();
            
            // Fetch all businesses from the database
            $businesses = Business::all();
            $data = [];
    
            foreach ($businesses as $business) {
                $nearestUser = null;
                $minDistance = PHP_FLOAT_MAX;
    
                foreach ($users as $user) {
                    $distance = $this->haversineGreatCircleDistance(
                        $user->latitude,
                        $user->longitude,
                        $business->latitude,
                        $business->longitude
                    );
    
                    if ($distance < $minDistance) {
                        $minDistance = $distance;
                        $nearestUser = $user;
                    }
                }
    
                if ($nearestUser) {
                    $data[] = [
                        'user_full_name' => $nearestUser->first_name . ' ' . $nearestUser->last_name,
                        'user_email' => $nearestUser->email,
                        'user_phone_number' => $nearestUser->phone_number,
                        'distance' => round($minDistance, 2),
                        'business_name' => $business->name,
                        'owner_first_name' => $business->owner_first_name,
                        'owner_last_name' => $business->owner_last_name,
                        'owner_email' => $business->owner_email,
                        'owner_number' => $business->owner_number,
                        'pincode' => $business->pincode,
                    ];
                }
            }
    
            // Sort the data by distance in ascending order
            usort($data, fn($a, $b) => $a['distance'] <=> $b['distance']);
    
            return response()->json([
                'code' => 200,
                'data' => $data,
                'message' => 'Data retrieved and sorted by distance successfully'
            ]);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
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
    
    
}

