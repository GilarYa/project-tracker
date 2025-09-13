<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'name',
        'description_progress'
    ];
    
    protected $casts = [
        'description_progress' => 'decimal:1'
    ];
    
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
    
    public function calculateProgress()
    {
        $totalWeight = $this->tasks()->sum('weight');
        
        if ($totalWeight == 0) {
            return 0.00;
        }
        
        $completedWeight = $this->tasks()->where('status', 'done')->sum('weight');
        
        $percentage = ($completedWeight / $totalWeight) * 100;
        return floor($percentage * 10) / 10; // Force round down to 1 decimal
    }
    
    public function updateProgress()
    {
        $this->description_progress = $this->calculateProgress();
        $this->save();
        
        return $this->description_progress;
    }
    
    public function calculateStatus()
    {
        $tasks = $this->tasks;
        
        if ($tasks->isEmpty()) {
            return 'draft';
        }
        
        $allDone = $tasks->every(function ($task) {
            return $task->status === 'done';
        });
        
        if ($allDone) {
            return 'done';
        }
        
        $hasInProgress = $tasks->contains(function ($task) {
            return $task->status === 'in_progress';
        });
        
        if ($hasInProgress) {
            return 'in_progress';
        }
        
        return 'draft';
    }
    
    public function updateStatus()
    {
        $this->status = $this->calculateStatus();
        $this->save();
        
        return $this->status;
    }
}
