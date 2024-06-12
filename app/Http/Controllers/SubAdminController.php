<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Helpers\DataTableHelper;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;
use Event;
use App\Events\SubAdminCreated;
class SubAdminController extends Controller
{
    use \App\Traits\TraitController;
    public function index(){
        // dd(userLogin()->getAllPermissions());
        return view('SubAdmin.index',[
            'title'=>'Sub Admin',
            'urlListData'=>routePut('subadmin.load-list'),'table' => 'tableSubAdmin'
        ]);
    }

    public function loadList(){
        try {
			$q = User::with("roles")->whereHas("roles", function($q) {
                $q->whereIn("name", ["sub admin"]);
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
                    // 'leads_count' => '<a href="' . route('team.leads', ['id' => $q->id]) . '">'. 'Total  ' . $q->hasLeads . '</a>', // Link to the new route
					'actions' => putNA(DataTableHelper::listActions([
						'edit' => auth()->user()->can('admin edit') ? routePut('subadmin.edit', ['id' => encrypt($single->getId())]) : '',
						'delete' => auth()->user()->can('admin delete') ? routePut('subadmin.delete',['id'=>encrypt($single->getId())]) :'',
                        'approve'=>auth()->user()->can('admin approve') && $single->getStatus()!= 1 ?routePut('subadmin.aprove',['id'=>encrypt($single->getId())]):'',
                        'reject'=>auth()->user()->can('admin reject') && $single->getStatus()!= 0 ?routePut('subadmin.reject',['id'=>encrypt($single->getId())]):'',
                        'block'=>auth()->user()->can('admin block') && $single->getStatus()!= 2 ?routePut('subadmin.block',['id'=>encrypt($single->getId())]):'',
                        'view' =>route('profile.view', ['id' => $single->id]),

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
        $permission = Permission::all()->pluck('name','name')->toArray();
        $subAdmin = new User();
        $selectedPermission = [];
        return view('SubAdmin.form',['heading'=>"Create",'title'=>"Create Sub Admin",'permission'=>$permission,'user'=>$subAdmin,'selectedPermission'=>$selectedPermission]);
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
                'permission' => 'required'
            ];
    
            // Add password validation only for new records
            if (!useId(request()->get('id'))) {
                $rules['password'] = 'required|string|confirmed|min:6';
            }
    
            $messages = [
                'first_name.required' => "Please Provide First Name",
                'last_name.required' => "Please Provide Last Name",
                'email.required' => "Please Provide Email",
                'email.email' => "Please provide valid email",
                'phone.digits' => "Phone number should be exactly :digits digits",
                'phone.required' => "Please provide phone number",
                'admin_title.required' => "Please select Title",
                'permission.required' => "Please Select Permission"
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
                    return $this->resp(0, "This email is already registered", [], 500);
                }
    
                $phoneExists = User::where('phone_number', request()->get('phone'))->exists();
                if ($phoneExists) {
                    return $this->resp(0, "This phone number is already registered", [], 500);
                }
            }
    
            $status = userLogin()->hasRole('admin') ? 1 : 0;
            $subadminData = [
                'first_name' => request()->get('first_name'),
                'last_name' => request()->get('last_name'),
                'email' => request()->get('email'),
                'phone_number' => request()->get('phone'),
                'title' => request()->get('admin_title'),
                'ti_status' => $status,
            ];
    
            if ($id) {
                // Update existing record
                $subadmin = User::find($id);
                $subadminData['ti_status'] = $subadmin->ti_status;
                User::where("id", $id)->update($subadminData);
                $subadmin->syncPermissions(request()->get('permission'));
    
                return $this->resp(1, "Subadmin Updated successfully", ['url' => routePut('subadmin.list')]);
            } else {
                // Create new record
                $password = request()->get('password');
                $subadminData['password'] = Hash::make($password);
                $subadmin = User::create($subadminData);
                if ($subadmin) {
                    $subadmin->assignRole('sub admin');
                    $subadmin->givePermissionTo(request()->get('permission'));
                    Event::dispatch(new SubAdminCreated($subadmin->getId(), $password));
                    return $this->resp(1, "Subadmin Created successfully", ['url' => routePut('subadmin.list')]);
                }
            }
    
        } catch (\Throwable $th) {
            return $this->resp(0, $th->getMessage(), [], 500);
        }
    }
    

    public function edit($id){
        try {
            if(useId($id)){
                $subAdmin = User::find(useId($id));
                if($subAdmin){
                    $permission = $subAdmin->getAllPermissions();
                    $selectedPermission=$permission->pluck('name')->toArray();
                    $permission = Permission::all()->pluck('name','name')->toArray();
                    return view('SubAdmin.form',['heading'=>"Edit",'title'=>"Edit Sub Admin",'permission'=>$permission,'user'=>$subAdmin,'selectedPermission'=>$selectedPermission]);
                }
                return redirect()->route('subadmin.list')->with('error',"sub admin not found");
            } else {
                return redirect()->route('subadmin.list')->with('error',"sub admin not found");
            }
        } catch (\Throwable $th) {
            return redirect()->route('subadmin.list')->with('error',"sub admin not found");
        }


    }

    public function delete($id){
        if ($single = User::find(useId($id))) {
            $single->delete();
            return $this->resp(1, getMsg('deleted', ['name' => "Subadmin"]));
        } else {
            return $this->resp(0, getMsg('not_found'));
        }
    }
    public function statusChange($id){
        if(routeCurrName()=="subadmin.aprove"){
            if ($single = User::find(useId($id))) {
            
                $single->ti_status= 1;
                $single->save();
                return $this->resp(1, getMsg('approve', ['name' => "Subadmin"]));
            } else {
                return $this->resp(0, getMsg('not_found'));
            }
        } elseif(routeCurrName()=="subadmin.reject") {
            if ($single = User::find(useId($id))) {
                $single->ti_status= 0;
                $single->save();
                return $this->resp(1, getMsg('reject', ['name' => "Subadmin"]));
            } else {
                return $this->resp(0, getMsg('not_found'));
            }
        } elseif(routeCurrName()=="subadmin.block"){
            if ($single = User::find(useId($id))) {
                $single->ti_status= 2;
                $single->save();
                return $this->resp(1, getMsg('block', ['name' => "Subadmin"]));
            } else {
                return $this->resp(0, getMsg('not_found'));
            }
        }

    }
}


