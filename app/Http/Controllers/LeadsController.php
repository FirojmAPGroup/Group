<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Business;
use App\Helpers\DataTableHelper;
use App\Models\Leads;
use App\Models\User;
use App\Events\LeadAssigned;
use Illuminate\Support\Facades\Auth;
use App\Notifications\UserNotifications;
use App\Helpers\DistanceHelper;
use Illuminate\Support\Facades\DB;

use PhpOffice\PhpSpreadsheet\IOFactory;

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
            $userId =$request->query('userid');
            $userLatitude = $request->query('latitude');
            $userLongitude = $request->query('longitude');
    
            if ($userId) {
                $user = User::find($userId);
                if (!$user) {
                    return response()->json(['message' => 'User not found']);
                }
                $userLatitude = $user->latitude;
                $userLongitude = $user->longitude;
            } else {
                if (is_null($userLatitude) || is_null($userLongitude)) {
                    return response()->json(['message' => 'Latitude and longitude are required'], 400);
                }
            }
    
            $businesses = Business::all();
            $distances = [];
           

            foreach ($businesses as $business) {
                $distance = DistanceHelper::haversineGreatCircleDistance(
                    $userLatitude,
                    $userLongitude,
                    $business->latitude,
                    $business->longitude
                );
    
                $distances[] = [
                    'business' => $business,
                    'distance' => $distance
                ];
            }
    
            usort($distances, fn($a, $b) => $a['distance'] <=> $b['distance']);
    
            $data = [];
            foreach ($distances as $distanceInfo) {
                $business = $distanceInfo['business'];
                $fullName = ($user->first_name ?? 'N/A') . ' ' . ($user->last_name ?? 'N/A');
                $data[] = [
                    'full_name' => $fullName,
                    // 'first_name' => $user->first_name ?? 'N/A',
                    // 'last_name' => $user->last_name ?? 'N/A',
                    'email' => $user->email ?? 'N/A',
                    'phone_number' => $user->phone_number ?? 'N/A',
                    'distance' => round($distanceInfo['distance'], 2),
                    'name' => $business->name ?? 'N/A',
                    'owner_first_name' => $business->owner_first_name ?? 'N/A',
                    'owner_last_name' => $business->owner_last_name ?? 'N/A',
                    'owner_email' => $business->owner_email ?? 'N/A',
                    'owner_number' => $business->owner_number ?? 'N/A',
                    'pincode' => $business->pincode ?? 'N/A',
                ];
            }
            if ($srch = DataTableHelper::search()) {
                $q = $businesses->where(function ($query) use ($srch) {
                    foreach (['full_name', 'owner_first_name','owner_last_name', 'owner_email','owner_number','pincode','email','phone_number','distance','name'] as $k => $v) {
                        if (!$k) $query->where($v, 'like', '%' . $srch . '%');
                        else $query->orWhere($v, 'like', '%' . $srch . '%');
                    }
                });
            }
    		$count = $businesses->count();
            // dd($data);
            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' =>$count ,
                'recordsFiltered' => $count,
                'data' => $data
            ]);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }
    

    // public function todayVisit(Request $request){
    //     try {

    //         $q = Business::query();
            
    //         $q = $q->leftJoin('leads','leads.business_id','business.id');
	// 		if ($srch = DataTableHelper::search()) {
	// 			$q = $q->where(function ($query) use ($srch) {
	// 				foreach (['name', 'owner_first_name','owner_last_name', 'owner_email','owner_number','pincode','city','state','country','area'] as $k => $v) {
	// 					if (!$k) $query->where($v, 'like', '%' . $srch . '%');
	// 					else $query->orWhere($v, 'like', '%' . $srch . '%');
	// 				}
	// 			});
	// 		}
    //         $q->whereDate('leads.visit_date',\Carbon\Carbon::today());
	// 		$count = $q->count();
            
	// 		if (DataTableHelper::sortBy() == 'ti_status') {
	// 			$q = $q->orderBy(DataTableHelper::sortBy(), DataTableHelper::sortDir() == 'asc' ? 'desc' : 'asc');
	// 		} else {
	// 			$q = $q->orderBy(DataTableHelper::sortBy(), DataTableHelper::sortDir());
	// 		}
	// 		$q = $q->skip(DataTableHelper::start())->limit(DataTableHelper::limit());

	// 		$data = [];
	// 		foreach ($q->get() as $single) {
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
    //                 'distance' => round($single->distance, 2), // Distance in kilometers rounded to 2 decimal places
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
            $leads[$value->id] = $value->name. "-" . $value->area;
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
            return $this->resp(1,"Lead Asign Successfuly",['url'=>routePut('leads.list')],200);
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
    
    // public function calculateDistance(Request $request)
    // {
    //     $userId = 1;
    //     //  $request->query('userId');
    //     $userLatitude = $request->query('latitude');
    //     $userLongitude = $request->query('longitude');

    //     if ($userId) {
    //         // If user ID is provided, find the user
    //         $user = User::find($userId);

    //         if (!$user) {
    //             return response()->json([
    //                 'message' => 'User not found'
    //             ], 404);
    //         }

    //         $userLatitude = $user->latitude;
    //         $userLongitude = $user->longitude;
    //     } else {
    //         if (is_null($userLatitude) || is_null($userLongitude)) {
    //             return response()->json([
    //                 'message' => 'Latitude and longitude are required'
    //             ], 400);
    //         }
    //     }

    //     $businesses = Business::all();
    //     // $businesses = Business::select('name', 'owner_first_name', 'owner_last_name', 'owner_email', 'owner_number')->get();

    //     $distances = [];

    //     foreach ($businesses as $business) {
    //         $distance = DistanceHelper::haversineGreatCircleDistance(
    //             $userLatitude,
    //             $userLongitude,
    //             $business->latitude,
    //             $business->longitude
    //         );

    //         $distances[] = [
    //             'business' => $business,
    //             'distance' => $distance
    //         ];
    //     }

    //     // Sort distances array by distance
    //     usort($distances, function ($a, $b) {
    //         return $a['distance'] <=> $b['distance'];
    //     });

    //     $nearestBusinesses = [];
    //     foreach ($distances as $index => $distanceInfo) {
    //         $nearestBusinesses[] = [
    //             'index' => $index + 1,
    //             'business' => $distanceInfo['business'],
    //             'distance' => $distanceInfo['distance']
    //         ];
    //     }

    // // Get user information if available
    //     $userInformation = [];
    //     if ($userId) {
    //         $userInformation = [
    //             'user_first_name' => $user->first_name,
    //             'user_last_name' => $user->last_name,
    //             'user_email' => $user->email,
    //             'user_phone' => $user->phone_number,
    //         ];
    //     }

    //     return response()->json([
    //         'userInformation'=>$userInformation,
    //         'nearest_business' => $distances[0]['business'],
    //         'nearest_distance km' => $distances[0]['distance'],
    //         'farthest_business' => end($distances)['business'],
    //         'farthest_distance' => end($distances)['distance'],
    //         'all_distances' => $distances
    //     ]);

    // }
    public function calculateDistance(Request $request)
    {
        try {
            $userId = 15;
            $userLatitude = $request->query('latitude');
            $userLongitude = $request->query('longitude');
    
            if ($userId) {
                $user = User::find($userId);
                if (!$user) {
                    return response()->json(['message' => 'User not found'], 404);
                }
                $userLatitude = $user->latitude;
                $userLongitude = $user->longitude;
            } else {
                if (is_null($userLatitude) || is_null($userLongitude)) {
                    return response()->json(['message' => 'Latitude and longitude are required'], 400);
                }
            }
    
            $businesses = Business::all();
            $distances = [];
    
            foreach ($businesses as $business) {
                $distance = DistanceHelper::haversineGreatCircleDistance(
                    $userLatitude,
                    $userLongitude,
                    $business->latitude,
                    $business->longitude
                );
    
                $distances[] = [
                    'business' => $business,
                    'distance' => $distance
                ];
            }
    
            usort($distances, fn($a, $b) => $a['distance'] <=> $b['distance']);
    
            $data = [];
            foreach ($distances as $distanceInfo) {
                $business = $distanceInfo['business'];
                $data[] = [
                    'first_name' => $user->first_name ?? 'N/A',
                    'last_name' => $user->last_name ?? 'N/A',
                    'email' => $user->email ?? 'N/A',
                    'phone_number' => $user->phone_number ?? 'N/A',
                    'distance' => round($distanceInfo['distance'], 2),
                    'Business name' => $business->name ?? 'N/A',
                    'owner_first_name' => $business->owner_first_name ?? 'N/A',
                    'owner_last_name' => $business->owner_last_name ?? 'N/A',
                    'owner_email' => $business->owner_email ?? 'N/A',
                    'owner_number' => $business->owner_number ?? 'N/A',
                    'pincode' => $business->pincode ?? 'N/A',
                ];
            }
            // dd($data);
            return response()->json([
                // 'draw' => $request->input('draw'),
                // 'recordsTotal' => count($data),
                // 'recordsFiltered' => count($data),
                'data' => $data
            ]);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }
}

