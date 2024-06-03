<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Helpers\DataTableHelper;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;
use Event;
use App\Models\Leads;
use App\Events\SubAdminCreated;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NewTeamNotifications;
use App\Notifications\AccountApprovalNotification;
class TeamsController extends Controller
{
    use \App\Traits\TraitController;
    
    public function index(){
        return view('Teams.index',[
            'title'=>'Teams',
            'urlListData'=>routePut('teams.loadList'),'table' => 'tableTeams'
        ]);
    }
    public function loadList(){
        try {
			$q = User::whereDoesntHave('roles')
            ->orWhereHas('roles', function ($query) {
                $query->where('name', 'user');
            });
			if ($srch = DataTableHelper::search()) {
				$q = $q->where(function ($query) use ($srch) {
					foreach (['email', 'phone_number', 'first_name','last_name'] as $k => $v) {
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
					'first_name' => putNA($single->first_name),
                    'last_name'=>putNA($single->last_name),
					'email' => putNA($single->email),
					'ti_status' => $single->listStatusBadge(),
					'created_at' => putNA($single->showCreated(1)),
					'actions' => putNA(DataTableHelper::listActions([
                        'edit' => auth()->user()->can('team edit') ? routePut('teams.edit', ['id' => encrypt($single->getId())]) : '',
						'delete' => auth()->user()->can('team delete') ? routePut('teams.delete',['id'=>encrypt($single->getId())]) :'',
                        'approve'=>auth()->user()->can('team approve') && $single->getStatus()!= 1  ?routePut('teams.aprove',['id'=>encrypt($single->getId())]):'',
                        'reject'=>auth()->user()->can('team reject')&& $single->getStatus()!= 0 ?routePut('teams.reject',['id'=>encrypt($single->getId())]):'',
                        'block'=>auth()->user()->can('team block') && $single->getStatus()!= 2 ?routePut('teams.block',['id'=>encrypt($single->getId())]):'',
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
    public function create(){
        $subAdmin = new User();
        return view('Teams.form',['heading'=>"Create",'title'=>"Create Team Member",'user'=>$subAdmin]);
    }

    public function save(){
        // dd(request()->all());
        try {
            $rules = [
                'first_name'=>'required',
                'last_name'=>'required',
                'email'=>'required',
                'phone'=>'required|digits:10',
                'admin_title'=>'required',
                'service_pincode'=>'required|digits:6'
            ];
            if(!useId(request()->get('id'))){
                $rules = [
                    'password'=>'required|string|confirmed|min:6'
                ];
            }
            $messages = [
                'first_name.required'=>"Please Provide First Name",
                'last_name.required'=>"Please Provide Last Name",
                'email.required'=>"Please Provide Email",
                'phone.required'=>"Please provide phone number",
                'phone.digits'=>"Phone number should be exact :digits",
                'admin_title.required'=>"Please select Title",
                'service_pincode.required'=>'Please provide service area pincode',
                'service_pincode.digits'=>"Service area code should be :digits"
            ];
            $validator = validate(request()->all(), $rules, $messages);
            if($validator){
                return $this->resp(0,$validator[0],[],500);
            }
            $status = userLogin()->hasRole('admin') ? 1 :0;
            $subadminData = [
                'first_name'=>request()->get('first_name'),
                'last_name'=>request()->get('last_name'),
                'email'=>request()->get('email'),
                'phone_number'=>request()->get('phone'),
                'title'=>request()->get('admin_title'),
                'service_pincode'=>request()->get('service_pincode')
            ];
            if(useId(request()->get('id'))){
                User::where("id",useId(request()->get('id')))->update($subadminData);
                return $this->resp(1,"Team Member Updated successfully",['url'=>routePut('teams.list')]);
            } else {
                $subadminData['ti_status']=$status;
                $subadminData['password']=Hash::make(request()->get('password'));
                $subadmin = User::create($subadminData);
                if($subadmin){
                    Event::dispatch(new SubAdminCreated($subadmin->getId(),request()->get('password')));
                    return $this->resp(1,"Team Member Created successfully",['url'=>routePut('teams.list')]);
                }
            }
        } catch (\Throwable $th) {
            // return $this->resp(0,"something went wrong. try again later",[],500);
            return $this->resp(0,$th->getMessage(),[],500);
        }
    }

    public function edit($id){
        try {
            if(useId($id)){
                $subAdmin = User::find(useId($id));
                if($subAdmin){
                    return view('Teams.form',['heading'=>"Edit",'title'=>"Edit Team Member",'user'=>$subAdmin]);
                }
                return redirect()->route('teams.list')->with('error',"Team Member not found");
            } else {
                return redirect()->route('teams.list')->with('error',"Team Member not found");
            }
        } catch (\Throwable $th) {
            return redirect()->route('teams.list')->with('error',"Team Member not found");
        }
    }

    public function delete($id){
        if ($single = User::find(useId($id))) {
            $single->delete();
            return $this->resp(1, getMsg('deleted', ['name' => "Team Member"]));
        } else {
            return $this->resp(0, getMsg('not_found'));
        }
    }

    public function statusChange($id){
        if(routeCurrName()=="teams.aprove"){
            if ($single = User::find(useId($id))) {
                $single->ti_status= 1;
                $single->save();
                // Send notification

                $single->notify(new AccountApprovalNotification([
                    'user_name' => $single->first_name. ' '. $single->last_name,
                    'message' => 'your account is approved '
                ]));
                return $this->resp(1, getMsg('approve', ['name' => "Team Member"]));
            } else {
                return $this->resp(0, getMsg('not_found'));
            }
        } elseif(routeCurrName()=="teams.reject") {
            if ($single = User::find(useId($id))) {
                $single->ti_status= 0;
                $single->save();
                // notifications
                $single->notify(new AccountApprovalNotification([
                    'user_name' => 'Admin',
                    'message' => 'your account is reject '
                ]));
                return $this->resp(1, getMsg('reject', ['name' => "Team Member"]));
            } else {
                return $this->resp(0, getMsg('not_found'));
            }
        } elseif(routeCurrName()=="teams.block"){
            if ($single = User::find(useId($id))) {
                $single->ti_status= 2;
                $single->save();
                // notification
                $single->notify(new AccountApprovalNotification([
                    'user_name' => 'Admin',
                    'message' => 'your account is blocked '
                ]));
                return $this->resp(1, getMsg('block', ['name' => "Team Member"]));
            } else {
                return $this->resp(0, getMsg('not_found'));
            }
        }
    }
 
    //   public function TeamReport(Request $request)
    // {
    //     $Options = [
    //         'total' => 'Total Visits',
    //         'completed' => 'Completed Visits',
    //         'pending' => 'Pending Visits',
    //     ];

    //     return view('TeamReport.TeamView', [
    //         'title' => 'Team | Report',
    //         'table' => 'tableReport',
    //         'urlListData' => routePut('teams.load'), // Updated route name
    //         'Options' => $Options,
    //         'selectedFilter' => $request->get('filter', 'total'), // Default to 'total'
    //     ]);
    // }

    // public function loadLists(Request $request)
    // {
    //     try {

            
    //         $filter = $request->get('filter', 'total'); // default to showing all visits
    //         $search = $request->input('search.value'); // Get the search query from the request
          
    //         // Retrieve leads query without executing it
    //         $leadsQuery = Leads::with(['business' => function ($query) {
    //             $query->select('id', 'owner_first_name','owner_last_name', 'name', 'owner_email');
    //         }, 
    //         'user' => function ($query) {
    //             $query->select('id', 'first_name', 'last_name', 'email');
    //         }
    //         ]
    //         );
    //         // $leadss=Leads::all();
    
    //         // Apply filter based on the selected option
    //         if ($filter == 'completed') {
    //             $leadsQuery->where('ti_status', 1);
    //         } elseif ($filter == 'pending') {
    //             $leadsQuery->where('ti_status', 0);
    //         }
    
    //         // Apply search filter if search query is provided
    //         if ($srch = DataTableHelper::search()) {
	// 			$q = $leadsQuery->where(function ($query) use ($srch) {
	// 				foreach (['email', 'phone_number', 'first_name','last_name'] as $k => $v) {
	// 					if (!$k) $query->where($v, 'like', '%' . $srch . '%');
	// 					else $query->orWhere($v, 'like', '%' . $srch . '%');
	// 				}
	// 			});
	// 		}
    
    //         // Get the count of filtered leads
    //         $count = $leadsQuery->count();
    //         // dd($Options,$leadsQuery);
    
    //         // Retrieve leads data after applying filters and search
    //         $leads = $leadsQuery->get();
    
    //         // Transform leads data
    //         $data = $leads->map(function ($lead) {
    //             $status = '';
    //             switch ($lead->ti_status) {
    //                 case 0:
    //                     $status = 'Pending';
    //                     break;
    //                 case 1:
    //                     $status = 'Completed';
    //                     break;
    //                 case 2:
    //                     $status = 'Total';
    //                     break;
    //                 default:
    //                     $status = 'Unknown';
    //                     break;
    //             }
    //             return [
    //                 'business_name' => $lead->business->name ?? '',
    //                 'owner_name' => $lead->business ? $lead->business->owner_first_name . ' ' . $lead->business->owner_last_name :'No Owner Name',
    //                 'owner_email' => $lead->business->owner_email ?? '',
    //                 'assigned_to' => $lead->user ? $lead->user->first_name . ' ' . $lead->user->last_name : 'Not Assigned',
    //                 'assigned_email' => $lead->user->email ?? '',
    //                 'Status' => $status,
    //                 'created_at' => $lead->created_at->format('Y-m-d'),
    //                 'assigned_on' => $lead->created_at->format('Y-m-d'),
    //             ];
    //         });
    //         return response()->json(['data' => $data]);

    //         // return $this->resp(1, '', [
    // 		// 	'draw' => request('draw'),
    // 		// 	'recordsTotal' => $count,
    // 		// 	'recordsFiltered' => $count,
    // 		// 	'data' => $data
    // 		// ]);

    //     } catch (\Throwable $th) {
    //         return $this->resp(0, exMessage($th), [], 500);
    //     }
    // }

    public function TeamReport(Request $request)
    {
        $Options = [
            'total' => 'Total Visits  ',
            'completed' => 'Completed Visits  ',
            'pending' => 'Pending Visits  ',
        ];
        $filter = $request->get('filter', 'total'); // default to showing all visits
        $search = $request->input('search.value'); // Get the search query from the request

        // Retrieve leads query without executing it
        $leadsQuery = Leads::with(['business' => function ($query) {
            $query->select('id', 'owner_first_name','owner_last_name', 'name', 'owner_email');},
             'user' => function ($query) {
            $query->select('id', 'first_name', 'last_name', 'email');}
        ])->orderBy('created_at', 'desc');


        if ($filter == 'completed') {
            $leadsQuery->where('ti_status', 1);
        } elseif ($filter == 'pending') {
            $leadsQuery->where('ti_status', 0);
        }

        // Apply search filter if search query is provided
        
        if ($search) {
            $leadsQuery->where(function ($query) use ($search) {
                $query->whereHas('business', function ($query) use ($search) {
                    $query->where('name', 'like', "%$search%")
                        ->orWhere('owner_name', 'like', "%$search%");
                })
                ->orWhereHas('user', function ($query) use ($search) {
                    $query->where('email', 'like', "%$search%");
                });
            })->get();
        }

        if ($srch = DataTableHelper::search()) {
            $q = $leadsQuery->where(function ($query) use ($srch) {
                foreach (['business_name', 'owner_name', 'owner_email','assigned_to','assigned_email','assigned_on','assigned_on'] as $k => $v) {
                    if (!$k) $query->where($v, 'like', '%' . $srch . '%');
                    else $query->orWhere($v, 'like', '%' . $srch . '%');
                }
            });
        }
        
        $count = $leadsQuery->count();

        if (DataTableHelper::sortBy() == 'ti_status') {
            $q = $leadsQuery->orderBy(DataTableHelper::sortBy(), DataTableHelper::sortDir() == 'asc' ? 'desc' : 'asc');
        } 
        // else {
        //     $q = $leadsQuery->orderBy(DataTableHelper::sortBy(), DataTableHelper::sortDir());
        // }
        // $q = $leadsQuery->skip(DataTableHelper::start())->limit(DataTableHelper::limit());


        $leads = $leadsQuery->get();

        $data = $leads->map(function ($lead) {
            $status = '';
            switch ($lead->ti_status) {
                case 0:
                    $status = 'Pending';
                    break;
                case 1:
                    $status = 'Completed';
                    break;
                case 2:
                    $status = 'Total';
                    break;
                default:
                    $status = 'Unknown';
                    break;
            }
            return [
                'business_name' => $lead->business->name ?? '',
                'owner_name' => $lead->business ? $lead->business->owner_first_name . ' ' . $lead->business->owner_last_name :'No Owner Name',
                'owner_email' => $lead->business->owner_email ?? '',
                'assigned_to' => $lead->user ? $lead->user->first_name . ' ' . $lead->user->last_name : 'Not Assigned',
                'assigned_email' => $lead->user->email ?? '',
                'Status' => $status,
                'visit_date'=>$lead->visit_date,
                'created_at' => $lead->created_at->format('Y-m-d'),
                'assigned_on' => $lead->created_at->format('Y-m-d'),
            ];
        });
        // dd($data);
        // Display results in the TeamView blade template
        return view('TeamReport.TeamView', [
            'title'=>'Teams | Reports',
            'leads' => $data, // Pass transformed data to view
            'Options' => $Options,
            'selectedFilter' => $filter,
            'table' => 'tableReport',
            'recordsTotal' => $count,
			'recordsFiltered' => $count,

        ]);
    }
    
    
    
}
