<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leads extends Model
{
    use \App\Traits\TraitModel;
    use HasFactory;
    protected $table = 'leads';
    protected $fillable = [
        'business_id','ti_status','latitude','longitude','remark','selfie','team_id','visit_date'
    ];

    const FOLDER="selfie";
    public function hasBusiness(){
        return $this->hasOne(Business::class,'id','business_id');
    }
    public function getBusiness(){
        return $this->hasBusiness;
    }

    public function hasUser(){
        return $this->hasOne(User::class,'id','team_id');
    }
    public function getUser(){
        return $this->hasUser;
    }

    public static function TotalVisits(){
        return self::count();
    }

    public static function completedVisit(){
        return self::where('ti_status',1)->count();
    }


    public static function pendingVisit(){
        return self::where('ti_status',2)->count();
    }

    public function getSelfieUrl(){
        return \App\Helpers\FileHelper::url($this->selfie,self::FOLDER);
    }

    public function getLeads(){
            
    }
    public function business() {
        return $this->belongsTo(Business::class, 'business_id');
        }
    
        public function user()
    {
        return $this->belongsTo(User::class, 'team_id');
    }
    public function notifications()
    {
        return $this->hasMany(CustomNotification::class);
    }
}
