<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

use Symfony\Component\HttpKernel\Profiler\Profile;

class ProfileController extends Controller
{
    public function index() {
        $user = Auth::user();
    
        return view('profile.form', [
            'user' => $user,
            'title'=>'Profile',
            'heading' => 'Profile',
        ]);
    }
    
    
        public function edit($id) {
            $user = User::findOrFail(decrypt($id));  // Find the user by decrypted ID
    
            // Ensure the logged in user can only edit their own profile unless they have specific permissions
            if ($user->id !== Auth::id() && !Auth::user()->can('edit any profile')) {
                abort(403, 'Unauthorized action.');
            }
    
            return view('profile.index', [
                'user' => $user,
                'heading' => 'Edit Profile'
            ]);
        }
    //     public function save(Request $request) {
    //         dd($request->all());
    //         $user = Auth::user();  // Get the currently authenticated user
    //         $this->validate($request, [
    //             'first_name' => 'required|string|max:255',
    //             'last_name' => 'required|string|max:255',
    //             'email' => 'required|email|max:255|unique:users,email,' . $user->id,
    //             'phone' => 'required|string|max:255',
    //             'admin_title' => 'required',
    //             'password' => 'nullable|string|min:6|max:15'
    //         ]);
        
    //         $user->first_name = $request->first_name;
    //         $user->last_name = $request->last_name;
    //         $user->email = $request->email;
    //         $user->phone_number = $request->phone;
    //         $user->title = $request->admin_title;
    //         // Update the password only if a new one is provided
    //         if ($request->filled('password')) {
    //             $user->password = Hash::make($request->password);
    //         }
    //         $user->save();
    //         // Redirect with success message
    //         return $this->resp(1, 'Profile updated successfully', ['url' => route('app.dashboard')]);
    // }
    
    public function save(Request $request) {
        $userId = Auth::id(); // Get the authenticated user's ID
        $user = User::findOrFail($userId); // Find the user by ID
    
        // Validate the request data
        $this->validate($request, [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'required|string|max:255',
            'admin_title' => 'required',
            'password' => 'nullable|string|min:6|max:15'
        ]);
    
        // Update the user's profile data
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->phone_number = $request->phone;
        $user->title = $request->admin_title;
        
        // Update the password only if a new one is provided
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        // Save the changes to the user's profile
        $user->save();
        $messages = " Profile updated successfuly" ;
        $url = useId(request()->get('id')) ? routePut('app.dashboard') : routePut('profile.view');
        return $this->resp(1,$messages,['url'=>$url],200);
        // return redirect()->route('app.dashboard')->with('success', 'Profile updated successfully');
    }
}
