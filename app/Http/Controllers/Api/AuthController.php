<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use  Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Lcobucci\JWT\Validation\Constraint\ValidAt;
use App\Notifications\LoginNotification;
use App\Notifications\NewUserNotification;
use App\Notifications\NewTeamNotifications;
use App\Notifications\RegistrationNotification;
use Illuminate\Support\Facades\Storage;
use App\Notifications\AccountApprovalNotification;

class AuthController extends Controller
{
    public function __construct()
    {
    }

    public function login(Request $request)
    {
        // dd($request->all());
        $validator  = validator::make($request->all(), [
            'phone_number' => 'required',
            'password' => 'required',
        ], [
            'phone_number.required' => 'Please provide phone number',
            'password.required' => 'please provide password',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'code' => 403,
                'message' => $validator->errors()->first(),
                'data' => []
            ], 403);
        }
        $user = User::where('phone_number', request()->get('phone_number'))->first();
        if ($user) {
            if ($user->ti_status == 1) {
                $credentials = $request->only('phone_number', 'password');

                $token = Auth::guard('api')->attempt($credentials);

                if (!$token) {
                    return response()->json([
                        'code' => 401,
                        'message' => 'please check you phone number and password',
                        'data' => [],
                    ], 401);
                }

                // notification method
                $loginTime = now()->toDateTimeString();
               
                $user->notify(new LoginNotification($user, $loginTime));
                // Check for recent status change notification
                $statusChangeNotification = null;

                if ($user->wasRecentlyApproved()) {
                    $statusChangeNotification = 'Hey ! Your account has been approved. Now explore the app.';
                } elseif ($user->wasRecentlyRejected()) {
                    $statusChangeNotification = 'Your account has been rejected.';
                } elseif ($user->wasRecentlyBlocked()) {
                    $statusChangeNotification = 'Your account has been blocked.';
                }
    
                if ($statusChangeNotification) {
                    $user->notify(new AccountApprovalNotification([
                        'user_name' => $user->first_name,
                        'message' => $statusChangeNotification
                    ]));
                }
                // Update last login time
                $user->last_login_at = now();
                $user->save();


                $user = Auth::guard('api')->user();
                return response()->json([
                    "code" => 200,
                    "message" => "Login suceesfully",
                    "data" => [
                        'user' => $user,
                        'authorization' => [
                            'token' => $token,
                            'type' => 'bearer'
                        ]
                    ]
                ]);
            } else {
                return response()->json([
                    "code" => 401,
                    "message" => "contact your administartor",
                    "data" => []
                ], 401);
            }
        } else {
            return response()->json([
                "code" => 404,
                "message" => "User not found",
                "data" => []
            ], 404);
        }
    }

    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'gender' => 'required|string|max:255',
                'phone_number' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|confirmed|min:6',
                'terms_accept' => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'code' => 403,
                    'message' => $validator->errors()->first(),
                    'data' => []
                ]);
            }
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'phone_number' => $request->phone_number,
                'email' => $request->email,
                'gender' => $request->gender,
                'password' => Hash::make($request->password),
            ]);
            // $user->assignRole('user');
             // notification method
                $loginTime = now()->toDateTimeString();
                $user->notify(new RegistrationNotification($user,$loginTime));

            return response()->json([
                'code' => 200,
                'message' => 'User created successfully',
                'data' => ['user' => $user],
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                "code" => $th->getCode(),
                "message" => $th->getMessage(),
                "data" => []
            ]);
        }
    }

    public function logout()
    {
        try {
            Auth::guard('api')->logout();
            return response()->json([
                "code" => 200,
                'message' => 'Successfully logged out',
                'data' => []
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                "code" => 500,
                'message' => $th->getMessage(),
                'data' => []
            ]);
        }
    }

    public function refresh()
    {
        try {
            return response()->json([
                "code" => 200,
                "data" => [
                    'user' => Auth::guard('api')->user(),
                    'authorisation' => [
                        'token' => Auth::guard('api')->refresh(),
                        'type' => 'bearer',
                    ]
                ],
                "message" => "Refresh Token generated successfully"
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                "code" => 500,
                "data" => [],
                "message" => "The token has been blacklisted or expired try to login again"
            ]);
        }
    }

    // public function forgot_password(Request $request)
    // {
    //     $input = $request->all();
    //     $rules = array(
    //         'email' => "required|email",
    //     );
    //     $validator = Validator::make($input, $rules);
    //     if ($validator->fails()) {
    //         $arr = array("status" => 400, "message" => $validator->errors()->first(), "data" => array());
    //     } else {
    //         try {
    //             $response = Password::sendResetLink($request->only('email'));
    //             switch ($response) {
    //                 case Password::RESET_LINK_SENT:
    //                     return response()->json(array("status" => 200, "message" => trans($response), "data" => array()));
    //                 case Password::INVALID_USER:
    //                     return response()->json(array("status" => 400, "message" => trans($response), "data" => array()));
    //             }
    //         } catch (\Throwable $ex) {
    //             $arr = array("status" => 400, "message" => $ex->getMessage(), "data" => []);
    //         } catch (\Throwable $ex) {
    //             $arr = array("status" => 400, "message" => $ex->getMessage(), "data" => []);
    //         }
    //     }
    //     return response()->json($arr);
    // }
    public function forgot_password(Request $request)
    {
        $input = $request->all();
        $rules = [
            'email' => 'required|email',
        ];
        
        $validator = Validator::make($input, $rules);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => $validator->errors()->first(),
                'data' => [],
            ]);
        }
        
        try {
            $response = Password::sendResetLink($request->only('email'));
            
            switch ($response) {
                case Password::RESET_LINK_SENT:
                    return response()->json([
                        'status' => 200,
                        'message' => trans($response),
                        'data' => [],
                    ]);
                    
                case Password::INVALID_USER:
                    return response()->json([
                        'status' => 400,
                        'message' => trans($response),
                        'data' => [],
                    ]);
            }
        } catch (\Exception $ex) {
            return response()->json([
                'status' => 400,
                'message' => $ex->getMessage(),
                'data' => [],
            ]);
        }
    }
    
    public function updateFCM()
    {
        try {
            $validator = Validator::make(
                request()->all(),
                [
                    'device_token' => 'required'
                ],
                [
                    'device_token.required' => "please provide device token"
                ]
            );
            if ($validator->fails()) {
                return response()->json([
                    'code' => 403,
                    'message' => $validator->errors()->first(),
                    'data' => []
                ], 403);
            }
            $user = User::find(Auth::guard('api')->user()->id);
            $user->fcm_token = request()->get('device_token');
            $user->save();
            return response()->json([
                'code' => 200,
                'message' => "FCM Token update successfully",
                'data' => ['user' => $user]
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'code' => $th->getCode(),
                'message' => $th->getMessage(),
                'data' => []
            ], $th->getCode());
        }
    }

    public function updateLocation()
    {
        try {
            $validator = Validator::make(
                request()->all(),
                [
                    'latitude' => 'required',
                    'longitude' => 'required'
                ],
                [
                    'latitude.,required' => "please provide latitude",
                    'longitude.required' => "please provide longitude"
                ]
            );
            if ($validator->fails()) {
                return response()->json([
                    'code' => 403,
                    'message' => $validator->errors()->first(),
                    'data' => []
                ], 403);
            }
            $user = User::find(Auth::guard('api')->user()->id);
            $user->latitude = request()->get('latitude');
            $user->longitude = request()->get('longitude');
            $user->update();
            return response()->json([
                'code' => 200,
                'message' => "Location update successfully",
                'data' => ['user' => $user]
            ], 200); 
        } catch (\Throwable $th) {
            return response()->json([
                'code' => $th->getCode(),
                'message' => $th->getMessage(),
                'data' => []
            ], $th->getCode());
        }
    }

    // public function updateProfile()
    // {
    //     try {
    //         //    dd(request()->all());
    //         $validator = Validator::make(request()->all(), [
    //             'first_name' => 'required',
    //             'last_name' => 'required',
    //             'birth_date' => 'required',
    //             'gender' => 'required',

    //         ], [
    //             'first_name.required' => 'please provide first name',
    //             'last_name.required' => 'please provide last name',
    //             'birth_date.required' => 'please provide birthdate',
    //             'gender.required' => 'please provide gender',
    //         ]);
    //         if ($validator->fails()) {
    //             return response()->json([
    //                 'code' => 403,
    //                 'message' => $validator->errors()->first(),
    //                 'data' => []
    //             ]);
    //         }
    //         $user = User::find(Auth::guard('api')->user()->id);
    //         if ($user) {
    //             $user->first_name = request()->get('first_name');
    //             $user->last_name = request()->get('last_name');
    //             $user->gender = request()->get('gender');
    //             $user->birth_date = request()->get('birth_date');
    //             if (request()->hasFile('profile_image')) {
    //                 $user->putFile('profile_image', request()->file('profile_image'), '');
    //             }
    //             $user->save();
    //             return response()->json([
    //                 'code' => 200,
    //                 'message' => 'User Updated Successfuly',
    //                 'data' => $user
    //             ]);
    //         } else {
    //             return response()->json([
    //                 'code' => 403,
    //                 'message' => "Unauthorised Access",
    //                 'data' => []
    //             ]);
    //         }
    //     } catch (\Throwable $th) {
    //         return response()->json([
    //             'code' => $th->getCode(),
    //             'message' => $th->getMessage(),
    //             'data' => []
    //         ]);
    //     }
    // }
    // public function updateProfile()
    // {
    //     try {
    //         $validator = Validator::make(request()->all(), [
    //             'first_name' => 'required',
    //             'last_name' => 'required',
    //             'birth_date' => 'required',
    //             'gender' => 'required',
    //         ], [
    //             'first_name.required' => 'please provide first name',
    //             'last_name.required' => 'please provide last name',
    //             'birth_date.required' => 'please provide birthdate',
    //             'gender.required' => 'please provide gender',
    //         ]);
    
    //         if ($validator->fails()) {
    //             return response()->json([
    //                 'code' => 403,
    //                 'message' => $validator->errors()->first(),
    //                 'data' => []
    //             ]);
    //         }
    
    //         $user = User::find(Auth::guard('api')->user()->id);
    //         if ($user) {
    //             $user->first_name = request()->get('first_name');
    //             $user->last_name = request()->get('last_name');
    //             $user->gender = request()->get('gender');
    //             $user->birth_date = request()->get('birth_date');
    
    //             if (request()->hasFile('profile_image')) {
    //                 // Delete old image if exists
    //                 if ($user->profile_image) {
    //                     Storage::delete($user->profile_image);
    //                 }
                    
    //                 // Store the new image
    //                 $path = request()->file('profile_image')->store('profile_images');
    //                 $user->profile_image = $path;
    //             }
    
    //             $user->save();
    
    //             // Add the base URL to the profile_image
    //             $profileImageUrl = $user->profile_image ? url('storage/' . $user->profile_image) : null;
    
    //             return response()->json([
    //                 'code' => 200,
    //                 'message' => 'User Updated Successfully',
    //                 'data' => [
    //                     'user' => $user,
    //                     'profile_image_url' => $profileImageUrl,
    //                     'profile_image_path' => $user->profile_image,
    //                 ]
    //             ]);
    //         } else {
    //             return response()->json([
    //                 'code' => 403,
    //                 'message' => "Unauthorized Access",
    //                 'data' => []
    //             ]);
    //         }
    //     } catch (\Throwable $th) {
    //         return response()->json([
    //             'code' => $th->getCode(),
    //             'message' => $th->getMessage(),
    //             'data' => []
    //         ]);
    //     }
    // }
    
    // public function updateProfile()
    // {
    //     try {
    //         $validator = Validator::make(request()->all(), [
    //             'first_name' => 'required',
    //             'last_name' => 'required',
    //             'birth_date' => 'required',
    //             'gender' => 'required',
    //         ], [
    //             'first_name.required' => 'please provide first name',
    //             'last_name.required' => 'please provide last name',
    //             'birth_date.required' => 'please provide birthdate',
    //             'gender.required' => 'please provide gender',
    //         ]);
    
    //         if ($validator->fails()) {
    //             return response()->json([
    //                 'code' => 403,
    //                 'message' => $validator->errors()->first(),
    //                 'data' => []
    //             ]);
    //         }
    
    //         $user = User::find(Auth::guard('api')->user()->id);
    //         if ($user) {
    //             $user->first_name = request()->get('first_name');
    //             $user->last_name = request()->get('last_name');
    //             $user->gender = request()->get('gender');
    //             $user->birth_date = request()->get('birth_date');
    
    //             if (request()->hasFile('profile_image')) {
    //                 // Delete old image if exists
    //                 if ($user->profile_image) {
    //                     Storage::delete($user->profile_image);
    //                 }
    
    //                 // Store the new image
    //                 $path = request()->file('profile_image')->store('profile_images');
    //                 $user->profile_image = $path;
    //             }
    
    //             $user->save();
    
    //             // Add the base URL to the profile_image
    //             $profileImageUrl = $user->getProfileImage();
    
    //             return response()->json([
    //                 'code' => 200,
    //                 'message' => 'User Updated Successfully',
    //                 'data' => [
    //                     'user' => $user,
    //                     'profile_image_url' => $profileImageUrl,
    //                 ]
    //             ]);
    //         } else {
    //             return response()->json([
    //                 'code' => 403,
    //                 'message' => "Unauthorized Access",
    //                 'data' => []
    //             ]);
    //         }
    //     } catch (\Throwable $th) {
    //         return response()->json([
    //             'code' => $th->getCode(),
    //             'message' => $th->getMessage(),
    //             'data' => []
    //         ]);
    //     }
    // }
    public function updateProfile()
    {
        try {
            $validator = Validator::make(request()->all(), [
                'first_name' => 'required',
                'last_name' => 'required',
                'birth_date' => 'required',
                'gender' => 'required',
            ], [
                'first_name.required' => 'please provide first name',
                'last_name.required' => 'please provide last name',
                'birth_date.required' => 'please provide birthdate',
                'gender.required' => 'please provide gender',
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'code' => 403,
                    'message' => $validator->errors()->first(),
                    'data' => []
                ]);
            }
    
            $user = User::find(Auth::guard('api')->user()->id);
            if ($user) {
                $user->first_name = request()->get('first_name');
                $user->last_name = request()->get('last_name');
                $user->gender = request()->get('gender');
                $user->birth_date = request()->get('birth_date');
    
                if (request()->hasFile('profile_image')) {
                    // Delete old image if exists
                    if ($user->profile_image) {
                        Storage::delete($user->profile_image);
                    }
    
                    // Store the new image
                    $path = request()->file('profile_image')->store('profile_images');
                    $user->profile_image = $path;
                }
    
                $user->save();
    
                // Generate the profile image URL
                $profileImageUrl = url('storage/' . $user->profile_image);
    
                return response()->json([
                    'code' => 200,
                    'message' => 'User Updated Successfully',
                    'data' => [
                        'user' => $user,
                        'profile_image_url' => $profileImageUrl,
                    ]
                ]);
            } else {
                return response()->json([
                    'code' => 403,
                    'message' => "Unauthorized Access",
                    'data' => []
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'code' => $th->getCode(),
                'message' => $th->getMessage(),
                'data' => []
            ]);
        }
    }
    
    
//   public function userProfile()
// {
//     try {
//         $user = Auth::guard('api')->user();
//         $userData = $user->toArray();
        
//         $userData['profile_image_url'] = $user->getProfileImage();

//         return response()->json([
//             'code' => 200,
//             'message' => 'User Detail Fetch successfuly',
//             'data' => $userData
//         ]);
//     } catch (\Throwable $th) {
//         return response()->json(
//             [
//                 'code' => $th->getCode(),
//                 'message' => $th->getMessage(),
//                 'data' => []
//             ]
//         );
//     }
// }


public function userProfile()
{
    try {
        $user = Auth::guard('api')->user();
        $userData = $user->toArray();
        
        // Check if the user has a profile image
        if ($user->profile_image) {
            // Generate the relative URL for the profile image
            $userData['profile_image_url'] = '/storage/' . $user->profile_image;
        } else {
            $userData['profile_image_url'] = null; // Or provide a default image URL if necessary
        }

        return response()->json([
            'code' => 200,
            'message' => 'User Detail Fetch successfully',
            'data' => $userData
        ]);
    } catch (\Throwable $th) {
        // Log the exception for debugging purposes
        \Log::error('Error fetching user profile: ' . $th->getMessage());
        
        return response()->json([
            'code' => $th->getCode(),
            'message' => $th->getMessage(),
            'data' => []
        ]);
    }
}




    
    public function verifyEmail()
    {
        try {
            $validator = Validator::make(request()->all(), [
                'email' => 'required|email'
            ], [
                'email.required' => 'please provide email',
                'email.email' => 'please provide valid email'
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'code' => 401,
                    'data' => [],
                    'message' => $validator->errors()->first()
                ], 401);
            }
            $email = User::where('email', request()->get('email'))->exists();
            if ($email) {
                return response()->json([
                    'code' => 403,
                    'message' => "Email already Register with other user",
                    'data' => []
                ]);
            } else {
                return response()->json([
                    'code' => 200,
                    'message' => "You can register with this email",
                    'data' => []
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'code' => $th->getCode(),
                'message' => $th->getMessage(),
                'data' => []
            ]);
        }
    }

    public function verifyMobile()
    {
        try {
            $validator = Validator::make(request()->all(), [
                'phone_number' => 'required|digits:10'
            ], [
                'phone_number.required' => 'please provide mobile number',
                'phone_number.digits' => 'please provide valid :digits digits mobile number'
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'code' => 401,
                    'data' => [],
                    'message' => $validator->errors()->first()
                ], 401);
            }
            $email = User::where('phone_number', request()->get('phone_number'))->exists();
            if ($email) {
                return response()->json([
                    'code' => 403,
                    'message' => "Mobile number already Register with other user",
                    'data' => []
                ]);
            } else {
                return response()->json([
                    'code' => 200,
                    'message' => "You can register with this mobile number",
                    'data' => []
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'code' => $th->getCode(),
                'message' => $th->getMessage(),
                'data' => []
            ]);
        }
    }

    public function updatePassword()
    {
        try {
            $validator = Validator::make(request()->all(), [
                'password' => 'required|confirmed',
                'mobile_number' => 'required|digits:10'
            ], [
                'mobile_number.required' => 'please provide mobile number',
                'mobile_number.digits' => 'please provide valid :digits mobile number',
                'password.required' => 'please provide password',
                'password.confirmed' => 'password and confirm password must be match'
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'code' => 401,
                    'data' => [],
                    'message' => $validator->errors()->first()
                ], 401);
            }
            if (request()->has('is_otp_verify') && request()->get('is_otp_verify') == 1) {
                $user = User::where('phone_number', request()->get('mobile_number'))->where('ti_status', 1)->first();
                if ($user) {
                    $user->password = Hash::make(request()->get('password'));
                    $user->save();
                    return response()->json([
                        'code' => 200,
                        'data' => [],
                        'message' => "password change successfully"
                    ], 200);
                } else {
                    return response()->json([
                        'code' => 401,
                        'data' => [],
                        'message' => "unathorised access"
                    ], 401);
                }
            } else {
                return response()->json([
                    'code' => 401,
                    'data' => [],
                    'message' => "unathorised access"
                ], 401);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'code' => $th->getCode(),
                'message' => $th->getMessage(),
                'data' => []
            ], $th->getCode());
        }
    }
}
