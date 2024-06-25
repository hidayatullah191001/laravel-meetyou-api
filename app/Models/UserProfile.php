<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'description'
    ];

    public function user(){
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function socialMedias(){
        return $this->hasMany(SocialMedia::class);
    }

    public function galleries(){
        return $this->hasMany(Gallery::class);
    }
}
