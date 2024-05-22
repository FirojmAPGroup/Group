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
                        // 'email' => 'required|email|max:255|unique:users,email,' . $user->id,
                        // 'phone' => 'required|string|max:255',
                        'admin_title' => 'required',
                        'gender'=>'required',
                        // 'password' => 'nullable|string|min:6|max:15|confirmed',
                        'birth_date'=>'required',

                    ]);
                
                    $user->first_name = $request->first_name;
                    $user->last_name = $request->last_name;
                    $user->gender = $request->gender;
                    $user->birth_date = $request->birth_date;
                    $user->title = $request->admin_title;
                    // Update the password only if a new one is provided
                    // if ($request->filled('password')) {
                    //     $user->password = Hash::make($request->password);
                    // }

        if (!$user) {
            abort(404, 'User not found');
        }

        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            // 'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'gender' => 'required',
            'birth_date'=>'required'
        ]);
        $user->save();

        // $user->update($validatedData);
        return redirect()->route('app.dashboard')->with('success', 'Profile updated successfully');
    }

}
