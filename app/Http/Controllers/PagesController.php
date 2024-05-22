<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pages;
use Illuminate\Support\Facades\Validator;
class PagesController extends Controller
{
    use \App\Traits\TraitController;
    public function aboutUsForm(){
        $pageContent = Pages::where('title','aboutus')->firstOrNew();
        if(isPost()){
            $validator = Validator::make(request()->all(),[
                'aboutus'=>'required',
            ],
            [
                'aboutus.required'=>'please provide content'
            ]);
            if($validator->fails()){
                return $this->resp(0,$validator->errors()->first(),[],403);
            }
            $msg = "";
            $page = Pages::where('title','aboutus')->first();
            if(!$page){
                $page = new Pages();
                $page->title ="aboutus";
                $page->content = request()->get('aboutus');
                $page->save();
                $msg = "page created Successfully";
                return $this->resp(1,$msg,['url'=>routePut('pages.about-us')],200);
            }
            $page->content = request()->get('aboutus');
            $msg = "page updated successfully";
            $page->save();
            return $this->resp(1,$msg,['url'=>routePut('pages.about-us')],200);
        }

        return view('pages.about-us',['title'=>"About Us Page",'page'=>$pageContent]);
    }

    public function privacyPolicyForm(){
        $pageContent = Pages::where('title','privacypolicy')->firstOrNew();
        if(isPost()){
            $validator = Validator::make(request()->all(),[
                'aboutus'=>'required',
            ],
            [
                'aboutus.required'=>'please provide content'
            ]);
            if($validator->fails()){
                return $this->resp(0,$validator->errors()->first(),[],403);
            }
            $page = Pages::where('title','privacypolicy')->first();
            $msg = "";
            if(!$page){
                $page = new Pages();
                $page->title ="privacypolicy";
                $page->content = request()->get('aboutus');
                $page->save();
                $msg = "page created Successfully";
                return $this->resp(1,$msg,['url'=>routePut('pages.privacy-policy')],200);
            }
            $page->content = request()->get('aboutus');
            $msg = "page updated successfully";
            $page->save();
            return $this->resp(1,$msg,['url'=>routePut('pages.privacy-policy')],200);
        }

        return view('pages.privacy-policy',['title'=>"Privacy Policy Page",'page'=>$pageContent]);
    }

    public function termsConditionForm(){
        $pageContent = Pages::where('title','termscondition')->firstOrNew();
        if(isPost()){
            $validator = Validator::make(request()->all(),[
                'aboutus'=>'required',
            ],
            [
                'aboutus.required'=>'please provide content'
            ]);
            if($validator->fails()){
                return $this->resp(0,$validator->errors()->first(),[],403);
            }
            $page = Pages::where('title','termscondition')->first();
            $msg = "";
            if(!$page){
                $page = new Pages();
                $page->title ="termscondition";
                $page->content = request()->get('aboutus');
                $page->save();
                $msg = "page created Successfully";
                return $this->resp(1,$msg,['url'=>routePut('pages.privacy-policy')],200);
            }
            $page->content = request()->get('aboutus');
            $msg = "page updated successfully";
            $page->save();
            return $this->resp(1,$msg,['url'=>routePut('pages.privacy-policy')],200);
        }

        return view('pages.terms-condition',['title'=>"Terms and Condition Page",'page'=>$pageContent]);
    }

    // public function contactUsForm(Request $request){
    //     $pageContent = Pages::where('title','contactus')->firstOrNew();
    //     if(isPost()){
    //         $validator = Validator::make(request()->all(),[
    //             'mobile_number'=>'required|digits:10|numeric',
    //             'email'=>'required|email',
    //             'address'=>'required',
    //         ],
    //         [
    //             'mobile_number.required'=>'please provide mobile number',
    //             'mobile_number.numeric'=>'please provide mobile number digits only',
    //             'mobile_number.digits'=>"phone number should be exact :digits number",
    //             'email.required'=>'please provide email',
    //             'email.email'=>'please provide valid email',
    //             'address.required'=>'please provide location'
    //         ]);
    //         if($validator->fails()){
    //             return $this->resp(0,$validator->errors()->first(),[],403);
    //         }
    //         $page = Pages::where('title','contactus')->first();
    //         $msg = "";
    //         if(!$page){
    //             $page = new Pages();
    //             $page->title ="contactus";
    //             $page->mobile_number = request()->get('mobile_number');
    //             $page->email = request()->get('email');
    //             $page->content = request()->get('address');
    //             $page->save();
    //             $msg = "page created Successfully";
    //             return $this->resp(1,$msg,['url'=>routePut('pages.contact-us')],200);
    //         }
    //         $page->content = request()->get('aboutus');
    //         $msg = "page updated successfully";
    //         $page->save();
    //         return $this->resp(1,$msg,['url'=>routePut('pages.contact-us')],200);
    //     }
    //     // dd($pageContent);
    //     return view('pages.contact-us',['title'=>"Contact Us Page",'page'=>$pageContent]);
    // }
    
    public function contactUsForm(Request $request)
{
    $pageContent = Pages::where('title', 'contactus')->firstOrNew();

    if ($request->isMethod('post')) {
        $validator = Validator::make($request->all(), [
            'mobile_number' => 'required|digits:10|numeric',
            'email' => 'required|email',
            'address' => 'required',
        ], [
            'mobile_number.required' => 'Please provide a mobile number',
            'mobile_number.numeric' => 'Please provide mobile number digits only',
            'mobile_number.digits' => 'Phone number should be exactly 10 digits',
            'email.required' => 'Please provide an email',
            'email.email' => 'Please provide a valid email',
            'address.required' => 'Please provide a location',
        ]);

        if ($validator->fails()) {
            return $this->resp(0, $validator->errors()->first(), [], 403);
        }

        $page = Pages::where('title', 'contactus')->first();
        $msg = "";

        if (!$page) {
            $page = new Pages();
            $page->title = "contactus";
        }

        $page->mobile_number = $request->get('mobile_number');
        $page->email = $request->get('email');
        $page->content = $request->get('address');
        $page->save();

        $msg = $page->wasRecentlyCreated ? "Page created successfully" : "Page updated successfully";

        return $this->resp(1, $msg, ['url' => routePut('pages.contact-us')], 200);
    }

    return view('pages.contact-us', ['title' => "Contact Us Page", 'page' => $pageContent]);
}

}

