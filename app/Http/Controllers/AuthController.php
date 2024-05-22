<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use  Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;
use App\Notifications\LoginNotification;

class AuthController extends Controller
{
    use \App\Traits\TraitController;
    public function login(){
        return view('Auth.login',['title'=>"Login"]);
    }

    public function loginSubmit(Request $request){
        $auth = auth()->guard(userGuard());
        $error = '';

        $user = User::with("roles")->whereHas("roles", function($q) {
            $q->whereIn("name", ["sub admin","admin"]);
        })->where('email',$request->email)->first();
        if($user){
            if($user->isActive()){
                if (Hash::check(request('password'), $user->password)) {
						$auth->login($user, request('rememberme') ? true : false);
                        $user->notify(new LoginNotification()); // Dispatch login notification

						return redirect(request('backurl', route('app.dashboard')))->with('success', 'Login successful');
				} else {
                    $error = "Password is incorrect";
                }
            } else {
                $error = 'User Not Active !';
            }
        } else {
            $error = 'User Not Found !';
        }
        return redirect()->back()->with('error', $error);
    }

    public function logout(){
        if (logId()) {
			auth()->guard(userGuard())->logout();
		}
		return redirect()->route('app.login');
    }

    public function forgotPassword(){
            return view('Auth.forgot-password',['title'=>"Reset Password"]);
    }
    
    public function forgotPasswordSubmit(Request $request){
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );
        return $status === Password::RESET_LINK_SENT
                    ? back()->with(['status' => __('Password reset link sent successfully!')])
                    : back()->withErrors(['email' => __($status)]);
    }

    public function passwordReset($token){
            return view('Auth.reset-password', ['token' => $token]);
    }

    public function passwordResetSubmit(Request $request){
        $validator = Validator::make($request->all(),[
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);
        if($validator->failed()){
            return redirect()->back()->with('error',$validator->errors()->first());
        }
            $status = Password::reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function (User $user, string $password) {
                    $user->forceFill([
                        'password' => Hash::make($password)
                    ])->setRememberToken(Str::random(60));

                    $user->save();

                    event(new PasswordReset($user));
                }
            );
            return $status === Password::PASSWORD_RESET
                        ? redirect()->route('app.login')->with('success', "Password Reset Successfullly")
                        : back()->with(['error' => __($status)]);
    }
}
