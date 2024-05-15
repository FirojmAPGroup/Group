<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Business;
use App\Helpers\DataTableHelper;
use App\Models\Leads;
use App\Models\User;
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

    public function todayVisit(){
        try {
			$q = Business::query();
            $q = $q->leftJoin('leads','leads.business_id','business.id');
			if ($srch = DataTableHelper::search()) {
				$q = $q->where(function ($query) use ($srch) {
					foreach (['name', 'owner_first_name','owner_last_name', 'owner_email','owner_number','pincode','city','state','country','area'] as $k => $v) {
						if (!$k) $query->where($v, 'like', '%' . $srch . '%');
						else $query->orWhere($v, 'like', '%' . $srch . '%');
					}
				});
			}
            $q->whereDate('leads.visit_date',\Carbon\Carbon::today());
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
                'owner_name'=>'required',
                'owner_number'=>'required',
                'owner_email'=>'required',
                'country'=>'required',
                'state'=>'required',
                'city'=>'required',
                'area'=>'required',
                'pincode'=>'required',
                'longitude'=>'required',
                'latitude'=>'required'
            ];
            $messages = [
                'name.required'=>"Please Provide Business Name",
                'owner_name.required'=>"Please Provide Owner Name",
                'owner_email.required'=>"Please Provide Owner Email",
                'owner_number.required'=>"Please provide Owner phone number",
                'country.required'=>"Please provide Country",
                'state.required'=>"Please provide state",
                'city.required'=>"Please provide city",
                'area.required'=>"Please provide area",
                'pincode.required'=>"please provide pincode",
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
            $business->owner_first_name = request()->get('owner_name');
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
            $leads[$value->id] = $value->name."-" . $value->area;
        }
        $rawUsers = User::whereDoesntHave('roles')->get();
        $users = [];
        foreach ($rawUsers as $value) {
            $users[$value->id]=$value->first_name." ".$value->last_name;
        }
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
                $title = "Pending Leads";
                return view('dashboard.leads',['title'=>$title,'table'=>'tblLeadStatus','urlListData'=>routePut('leads.getleadList',['status'=>$status])]);
            } elseif($status == "completed"){
                $title = "Completed Leads";
                return view('dashboard.leads',['title'=>$title,'table'=>'tblLeadStatus','urlListData'=>routePut('leads.getleadList',['status'=>$status])]);
            } elseif($status == "total") {
                $title = "Total Leads";
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
}

