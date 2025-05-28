<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KnowledgeSourceRole extends Model
{
    use HasFactory;

    protected $fillable = ['source_identifier', 'role_id'];
    public $timestamps = false;

    // This relationship still "works" in Eloquent,
    // it just won't have DB-level integrity enforcement.
    public function role()
    {
        return $this->belongsTo(Role::class); // Assumes Role model's primary key is 'id'
    }
}