<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubTask extends Model
{
    use HasFactory;

    protected $table = 'sub_tasks';
    protected $primaryKey = 'id';

    public function taskInfo() {
        return $this->belongsTo('App\Models\Task', 'task_id', 'id');
    }
}
