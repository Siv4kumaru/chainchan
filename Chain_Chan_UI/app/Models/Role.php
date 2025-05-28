<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = ['name', 'description'];
    public $timestamps = false;
    
    // Optional: If you want to see which users have a certain role
    // This assumes your users.role column stores the role NAME (string)
    public function users()
    {
        return $this->hasMany(User::class, 'role', 'name');
    }
}