<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categories extends Model
{
    use HasFactory;

    protected $table = 'category';
    protected $primaryKey = 'id';

    public function postBox() {
        return $this->hasMany('App\Models\PostBoxes', 'category_id', 'id');
    }
}
