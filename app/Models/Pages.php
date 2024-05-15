<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pages extends Model
{
    use \App\Traits\TraitModel;
    use HasFactory;
    protected $table = "pages";

    protected $fillable = [
        'title',
        'content',
        'mobile',
        'email'
    ];

    protected $casts = [
        'created_at'=>"datetime",
        'updated_at'=>'datetime'
    ];
}
