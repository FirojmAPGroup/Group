<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Business;
use Illuminate\Http\Request;
use App\Models\Leads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class LeadsController extends Controller
{
    public function getPendingLeads(){
        $user = Auth::guard('api')->user();
        // dd($user);
        $leads = Leads::with('hasBusiness')->where('team_id',$user->id)->where('leads.ti_status',0)->paginate(5);
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

    public function getDoneLeads(){
        $user = Auth::guard('api')->user();
        dd($user);
        $leads = Leads::with('hasBusiness')->where('team_id',$user->id)->where('leads.ti_status',2)->paginate(5);
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

    public function todayLeads(){
        try {
        $user = Auth::guard('api')->user();
        $leads = Leads::with('hasBusiness')->where('team_id',$user->id)->where('leads.ti_status',0)->whereDate('updated_at',Carbon::today())->paginate(5);
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
        } catch (\Throwable $th) {
            return response()->json([
                'code'=>$th->getCode(),
                'data'=>[],
                'message'=>$th->getMessage()
            ]);
        }
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
            'first_name'=>'required|max:25',
            'last_name'=>'required|max:25',
            'mobile'=>'required|digits:10',
            'email'=>'required|email',
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
            'owner_first_name'=>request()->get('first_name'),
            'owner_last_name'=>request()->get('last_name'),
            'owner_number'=>request()->get('mobile'),
            'owner_email'=>request()->get('email'),
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

    public function updateLead($id){
        try {
            $validator = Validator::make(request()->all(),[
                'remarks'=>'required|max:100',
                'selfie'=>'required|mimes:png,jpg',
                'latitude'=>'required',
                'longitude'=>'required'
            ]);
            if($validator->fails()){
                return response()->json([
                    'code'=>401,
                    'data'=>[],
                    'message'=>$validator->errors()->first()
                ],401);
            }
            $lead = Leads::find($id);
            if($lead){
                $lead->remark = request()->get('remarks');
                $lead->putFile('selfie',request()->file('selfie'),'');
                $lead->latitude = request()->get('latitude');
                $lead->longitude = request()->get('longitude');
                $lead->ti_status = request()->get('status');
                $lead->save();
                $leadData= $lead->toArray();
                $leadData['selfie']=$lead->getSelfieUrl();
                $leadData['hasBusiness']= $lead->getBusiness();
                return response()->json([
                    'code'=>200,
                    'data'=>$leadData,
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
