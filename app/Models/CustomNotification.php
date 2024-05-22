<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomNotification extends Model
{
    protected $table = 'custom_notifications'; // Specify the custom table name

    protected $fillable = [
        'user_id',
        'message',
    ];
}
