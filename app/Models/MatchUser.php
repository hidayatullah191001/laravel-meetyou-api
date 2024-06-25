<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatchUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_match_id'
    ];

    public function user(){
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function userMatch(){
        return $this->hasOne(User::class, 'id', 'user_match_id');
    }
}
