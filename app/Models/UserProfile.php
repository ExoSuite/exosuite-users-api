<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    public $incrementing = false;

    protected $fillable = [
        'id', 'birthday', 'city', 'description'
    ];

    protected $hidden = [
        'id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
