<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_profile_id', 
        'image_gallery',
    ];

    // public function user_profile(){
    //     return $this->hasOne(UserProfile::class, 'id', 'user_profile_id');
    // }

    public function user(){
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
