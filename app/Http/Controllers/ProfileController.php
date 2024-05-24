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
    public function show()
    {
        // Assuming you are using authentication
        $user = auth()->user();

        return view('profile.form', [
            'user' => $user,
            'title' => 'Profile',
            'heading' => 'View Profile',
        ]);
    }

    public function edit($id)
    {
        $user = User::find($id);

        if (!$user) {
            abort(404, 'User not found');
        }

        return view('profile.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);

        $this->validate($request, [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'admin_title' => 'required',
            'gender' => 'required',
            'birth_date' => 'required',

        ]);

        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->gender = $request->gender;
        $user->birth_date = $request->birth_date;
        $user->title = $request->admin_title;


        if (!$user) {
            abort(404, 'User not found');
        }

        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'gender' => 'required',
            'birth_date' => 'required'
        ]);
        $user->save();

        // $user->update($validatedData);
        return response()->json(['success' => 'Profile updated successfully']);
    }

    public function updatepassword($id)
    {
        $user = User::find($id);

        if (!$user) {
            abort(404, 'User not found');
        }

        return view('profile.updatepassword', compact('user'));
    }

    public function changepassword(Request $request, $id)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:6|confirmed',
        ]);
    
        $user = User::find($id);
    
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Your current password does not match in our records.']);
        }
    
        $user->password = Hash::make($request->new_password);
        $user->save();
        // return $this->resp(1,"Password Updated successfully",['url'=>routePut('app.dashboard')]);
        return redirect()->route('app.dashboard')->with('success', 'Password successfully updated.');

        // return redirect()->back()->with('success', 'Password successfully updated.');
    }
    
}
