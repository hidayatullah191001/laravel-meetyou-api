<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialMedia extends Model
{
    use HasFactory;
    // protected $with = ['userProfile'];

    protected $fillable = [
        'type',
        'user_profile_id',
        'account',
    ];

    public function userProfile()
    {
        return $this->belongsTo(UserProfile::class);
    }

}
