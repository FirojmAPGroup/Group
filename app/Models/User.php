<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use App\Traits\TraitModel;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable,HasRoles,TraitModel;
    const FOLDER= "user";
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'gender',
        'title',
        'ti_status',
        'phone_number',
        'password',
        'fcm_token',
        'latitude',
        'longitude',
        'gender',
        'profile_image',
        'birth_date',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];


    public function hasLeads(){
        return $this->hasMany(Leads::class,'team_id','id');
    }

    public function getLeads(){
        return $this->hasLeads;
    }

    public static function totalTeam(){
       return self::whereDoesntHave('roles')
                    ->orWhereHas('roles', function ($query) {
                    $query->where('name', 'user');
                })->where('ti_status',1)->count();
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function getProfileImage(){
       return \App\Helpers\FileHelper::url($this->profile_image,self::FOLDER);
    }

    public static function getUserIdUsingPincode($pincode){
        $user = self::where('service_pincode',$pincode)->where('ti_status',1)->first();
        return $user ? $user->getId() : null;
    }

    public function notifications()
    {
        return $this->hasMany(CustomNotification::class);
    }

    public function unreadNotifications()
    {
        return $this->hasMany(CustomNotification::class);
    }
}
