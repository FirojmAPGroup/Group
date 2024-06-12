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

    public function index()
    {
        return view('Teams.index', [
            'title' => 'Teams',
            'urlListData' => routePut('teams.loadList'), 'table' => 'tableTeams'
        ]);
    }
  
    public function loadList()
    {
        try {
            $q = User::whereDoesntHave('roles')
                ->orWhereHas('roles', function ($query) {
                    $query->where('name', 'user');
                });

            if ($srch = DataTableHelper::search()) {
                $q = $q->where(function ($query) use ($srch) {
                    foreach (['email', 'phone_number', 'first_name', 'last_name'] as $k => $v) {
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

            // Fetch users with their leads count using eager loading
            $users = $q->withCount('hasLeads')->get();

            $data = [];
            foreach ($users as $user) {
                $data[] = [
                    'first_name' => putNA($user->first_name),
                    'last_name' => putNA($user->last_name),
                    'email' => putNA($user->email),
                    'ti_status' => $user->listStatusBadge(),
                    'created_at' => putNA($user->showCreated(1)),
                    'leads_count' => '<a href="' . route('team.leads', ['id' => $user->id]) . '" style="color: black !important; font-weight: bold;">' . $user->has_leads_count . '</a>',
                    'actions' => putNA(DataTableHelper::listActions([
                        'edit' => auth()->user()->can('team edit') ? routePut('teams.edit', ['id' => encrypt($user->id)]) : '',
                        'delete' => auth()->user()->can('team delete') ? routePut('teams.delete', ['id' => encrypt($user->id)]) : '',
                        'approve' => auth()->user()->can('team approve') && $user->getStatus() != 1 ? routePut('teams.aprove', ['id' => encrypt($user->id)]) : '',
                        'reject' => auth()->user()->can('team reject') && $user->getStatus() != 0 ? routePut('teams.reject', ['id' => encrypt($user->id)]) : '',
                        'block' => auth()->user()->can('team block') && $user->getStatus() != 2 ? routePut('teams.block', ['id' => encrypt($user->id)]) : '',
                        'view' => route('profile.view', ['id' => $user->id]),
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

  
public function showLeads($id, Request $request)
{
    try {
        $user = User::with('hasLeads.business')->findOrFail($id); // Eager load leads and their business

        // Fetch leads with simple search and pagination
        $query = $user->hasLeads()->with('business');

        // If there's a search term, filter results accordingly
        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('business', function ($q) use ($search) {
                $q->where('name', 'LIKE', '%' . $search . '%');
            });
        }

        $leads = $query->get();

        return view('teams.leads', compact('user', 'leads'));
    } catch (\Throwable $th) {
        return redirect()->back()->withErrors(['error' => exMessage($th)]);
    }
}


    public function create()
    {
        $subAdmin = new User();
        return view('Teams.form', ['heading' => "Create", 'title' => "Create Team Member", 'user' => $subAdmin]);
    }

   
    public function save()
    {
        try {
            $rules = [
                'first_name' => 'required',
                'last_name' => 'required',
                'email' => 'required|email',
                'phone' => 'required|digits:10',
                'admin_title' => 'required',
                'service_pincode' => 'required|digits:6'
            ];
    
            // Add password validation only for new records
            if (!useId(request()->get('id'))) {
                $rules['password'] = 'required|string|confirmed|min:6';
            }
    
            $messages = [
                'first_name.required' => "Please Provide First Name",
                'last_name.required' => "Please Provide Last Name",
                'email.required' => "Please Provide Email",
                'email.email' => "Please Provide a Valid Email Address",
                'phone.required' => "Please provide phone number",
                'phone.digits' => "Phone number should be exactly :digits digits",
                'admin_title.required' => "Please select Title",
                'service_pincode.required' => 'Please provide service area pincode',
                'service_pincode.digits' => "Service area code should be :digits digits"
            ];
    
            $validator = validate(request()->all(), $rules, $messages);
    
            if ($validator) {
                return $this->resp(0, $validator[0], [], 500);
            }
    
            $id = useId(request()->get('id'));
            $isNewRecord = !$id;
    
            // Check if email and phone number already exist only for new records
            if ($isNewRecord) {
                $emailExists = User::where('email', request()->get('email'))->exists();
                if ($emailExists) {
                    return $this->resp(0, "This email is already registered. Please provide other email ", [], 500);
                }
    
                $phoneExists = User::where('phone_number', request()->get('phone'))->exists();
                if ($phoneExists) {
                    return $this->resp(0, "This phone number is already registered. Please provide a new mobile number", [], 500);
                }
            }
    
            $status = userLogin()->hasRole('admin') ? 1 : 0;
            $subadminData = [
                'first_name' => request()->get('first_name'),
                'last_name' => request()->get('last_name'),
                'email' => request()->get('email'),
                'phone_number' => request()->get('phone'),
                'title' => request()->get('admin_title'),
                'service_pincode' => request()->get('service_pincode')
            ];
    
            if ($id) {
                // Update existing record
                User::where("id", $id)->update($subadminData);
                return $this->resp(1, "Team Member Updated successfully", ['url' => routePut('teams.list')]);
            } else {
                // Create new record
                $subadminData['ti_status'] = $status;
                $subadminData['password'] = Hash::make(request()->get('password'));
                $subadmin = User::create($subadminData);
                if ($subadmin) {
                    Event::dispatch(new SubAdminCreated($subadmin->getId(), request()->get('password')));
                    return $this->resp(1, "Team Member Created successfully", ['url' => routePut('teams.list')]);
                }
            }
        } catch (\Throwable $th) {
            return $this->resp(0, $th->getMessage(), [], 500);
        }
    }
    
    
    public function edit($id)
    {
        try {
            if (useId($id)) {
                $subAdmin = User::find(useId($id));
                if ($subAdmin) {
                    return view('Teams.form', ['heading' => "Edit", 'title' => "Edit Team Member", 'user' => $subAdmin]);
                }
                return redirect()->route('teams.list')->with('error', "Team Member not found");
            } else {
                return redirect()->route('teams.list')->with('error', "Team Member not found");
            }
        } catch (\Throwable $th) {
            return redirect()->route('teams.list')->with('error', "Team Member not found");
        }
    }

    public function delete($id)
    {
        if ($single = User::find(useId($id))) {
            $single->delete();
            return $this->resp(1, getMsg('deleted', ['name' => "Team Member"]));
        } else {
            return $this->resp(0, getMsg('not_found'));
        }
    }

    public function statusChange($id)
    {
        if (routeCurrName() == "teams.aprove") {
            if ($single = User::find(useId($id))) {
                $single->ti_status = 1;
                $single->save();
                // Send notification

                $single->notify(new AccountApprovalNotification([
                    'user_name' => $single->first_name . ' ' . $single->last_name,
                    'message' => 'your account is approved '
                ]));
                return $this->resp(1, getMsg('approve', ['name' => "Team Member"]));
            } else {
                return $this->resp(0, getMsg('not_found'));
            }
        } elseif (routeCurrName() == "teams.reject") {
            if ($single = User::find(useId($id))) {
                $single->ti_status = 0;
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
        } elseif (routeCurrName() == "teams.block") {
            if ($single = User::find(useId($id))) {
                $single->ti_status = 2;
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

    // public function TeamReport(Request $request)
    // {
    //     $Options = [
    //         'total' => 'Total Visits',
    //         'completed' => 'Completed Visits',
    //         'pending' => 'Pending Visits',
    //     ];
    //     $filter = $request->get('filter', 'total');
    //     $search = $request->input('search.value');

    //     $leadsQuery = Leads::with(['business' => function ($query) {
    //         $query->select('id', 'owner_first_name', 'owner_last_name', 'name', 'owner_email');
    //     }, 'user' => function ($query) {
    //         $query->select('id', 'first_name', 'last_name', 'email');
    //     }])->orderBy('created_at', 'desc');

    //     if ($filter == 'completed') {
    //         $leadsQuery->where('ti_status', 1);
    //     } elseif ($filter == 'pending') {
    //         $leadsQuery->where('ti_status', 2);
    //     }

    //     if ($search) {
    //         $leadsQuery->where(function ($query) use ($search) {
    //             $query->whereHas('business', function ($query) use ($search) {
    //                 $query->where('name', 'like', "%$search%")
    //                     ->orWhere('owner_first_name', 'like', "%$search%")
    //                     ->orWhere('owner_last_name', 'like', "%$search%");
    //             })
    //                 ->orWhereHas('user', function ($query) use ($search) {
    //                     $query->where('first_name', 'like', "%$search%")
    //                         ->orWhere('last_name', 'like', "%$search%")
    //                         ->orWhere('email', 'like', "%$search%");
    //                 });
    //         });
    //     }

    //     if ($srch = DataTableHelper::search()) {
    //         $q = $leadsQuery->where(function ($query) use ($srch) {
    //             foreach (['business_name', 'owner_name', 'owner_email', 'assigned_to', 'assigned_email', 'assigned_on', 'assigned_on'] as $k => $v) {
    //                 if (!$k) $query->where($v, 'like', '%' . $srch . '%');
    //                 else $query->orWhere($v, 'like', '%' . $srch . '%');
    //             }
    //         });
    //     }

    //     $count = $leadsQuery->count();

    //     if (DataTableHelper::sortBy() == 'ti_status') {
    //         $q = $leadsQuery->orderBy(DataTableHelper::sortBy(), DataTableHelper::sortDir() == 'asc' ? 'desc' : 'asc');
    //     }

    //     $leads = $leadsQuery->get();

    //     $data = $leads->map(function ($lead) {
    //         return [
    //             'id' => $lead->id,
    //             'business_name' => $lead->business->name ?? '',
    //             'owner_name' => $lead->business ? $lead->business->owner_first_name . ' ' . $lead->business->owner_last_name : 'No Owner Name',
    //             'owner_email' => $lead->business->owner_email ?? '',
    //             'assigned_to' => $lead->user ? $lead->user->first_name . ' ' . $lead->user->last_name : 'Not Assigned',
    //             'assigned_email' => $lead->user->email ?? '',
    //             'Status' => $lead->leadStatus(),
    //             'visit_date' => $lead->visit_date,
    //             'created_at' => $lead->created_at->format('Y-m-d'),
    //             'assigned_on' => $lead->created_at->format('Y-m-d'),
    //         ];
    //     });

    //     return view('TeamReport.TeamView', [
    //         'title' => 'Teams | Reports',
    //         'leads' => $data,
    //         'Options' => $Options,
    //         'selectedFilter' => $filter,
    //         'table' => 'tableReport',
    //         'recordsTotal' => $count,
    //         'recordsFiltered' => $count,
    //         'search' => $search,
    //     ]);
    // }
    public function TeamReport(Request $request)
    {
        $Options = [
            'day_wise' => 'Date',
            'team_member_wise' => 'Team Member ',
            'conversation_wise' => 'Conversation ',
            'overall' => 'All'
        ];
    
        $filter = $request->get('filter', 'overall');
        $search = $request->input('search.value');
    
        // Initialize the leads query
        $leadsQuery = Leads::with(['business', 'user'])
                            ->orderBy('created_at', 'desc');
    
        // Apply filters based on the selected option
        switch ($filter) {
            case 'day_wise':
                $selectedDate = $request->input('selected_date');
                // Check if a date is selected
                if ($selectedDate) {
                    // Filter leads for the selected date
                    $leadsQuery->whereDate('created_at', $selectedDate);
                }
                break;
                case 'team_member_wise':
                    // Fetch team members
                    $teamMembers = User::whereDoesntHave('roles')
                                        ->orWhereHas('roles', function ($query) {
                                            $query->where('name', 'user');
                                        })
                                        ->get();
                    // Filter leads by the selected team member
                    $selectedMemberId = $request->input('selected_member');
                    if ($selectedMemberId) {
                        $leadsQuery->where('team_id', $selectedMemberId); // Adjust the column name here
                    }
                    break;                
                case 'conversation_wise':
                    // Check if conversation type is selected
                    $conversationType = $request->input('conversation_type');
                    if ($conversationType === 'pending') {
                        $leadsQuery->where('ti_status', 2);
                    } elseif ($conversationType === 'complete') {
                        $leadsQuery->where('ti_status', 1);
                    }
                    break;
            default:
                // No additional filtering required for overall report
                break;
        }
    
        // Apply search filter
        if ($search) {
            $leadsQuery->where(function ($query) use ($search) {
                // Add relevant fields for searching
                $query->whereHas('business', function ($query) use ($search) {
                    $query->where('name', 'like', "%$search%")
                          ->orWhere('owner_first_name', 'like', "%$search%")
                          ->orWhere('owner_last_name', 'like', "%$search%");
                })->orWhereHas('user', function ($query) use ($search) {
                    $query->where('first_name', 'like', "%$search%")
                          ->orWhere('last_name', 'like', "%$search%")
                          ->orWhere('email', 'like', "%$search%");
                });
            });
        }
    
        // Fetch leads data
        $leads = $leadsQuery->get();
    
        // Map the data
        $data = $leads->map(function ($lead) {
            return [
                'id' => $lead->id,
                'business_name' => $lead->business->name ?? '',
                'owner_name' => $lead->business ? $lead->business->owner_first_name . ' ' . $lead->business->owner_last_name : 'No Owner Name',
                'owner_email' => $lead->business->owner_email ?? '',
                'assigned_to' => $lead->user ? $lead->user->first_name . ' ' . $lead->user->last_name : 'Not Assigned',
                'assigned_email' => $lead->user->email ?? '',
                'Status' => $lead->leadStatus(), // Ensure consistent naming
                'visit_date' => $lead->visit_date,
                'created_at' => $lead->created_at->format('Y-m-d'),
                'assigned_on' => $lead->created_at->format('Y-m-d'),
            ];
        });
        $selectedDate = $request->input('selected_date', ''); // Fetch the selected date from the request parameters, defaulting to an empty string if not set

        return view('TeamReport.TeamView', [
            'title' => 'Teams | Reports',
            'leads' => $data,
            'Options' => $Options,
            'selectedFilter' => $filter,
            'table' => 'tableReport', // Assuming this is the table ID for DataTables
            'search' => $search,
            'selectedDate' => $selectedDate, // Pass the selected date to the view
            'teamMembers' => $teamMembers ?? null, // Pass team members to the view
        ]);
    }
    
    

    public function showDetail($id)
    {

        $lead = Leads::with(['business', 'user'])->findOrFail($id);

        // Return your detailed view or data
        return view('TeamReport.DetailView', compact('lead'));
    }
}
