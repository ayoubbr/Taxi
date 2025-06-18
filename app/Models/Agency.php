<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agency extends Model
{
    use HasFactory;

    public function users()
    {
        return $this->hasMany(User::class);
    }
    
    public function taxis()
    {
        return $this->hasMany(Taxi::class);
    }
}
