<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $table = 'tasks';
    protected $primaryKey = 'id';

    public function subTasks() {
        return $this->hasMany('App\Models\SubTask', 'task_id', 'id');
    }
}
