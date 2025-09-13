<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'name',
        'status',
        'project_id',
        'weight'
    ];
    
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
