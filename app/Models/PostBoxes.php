<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostBoxes extends Model
{
    use HasFactory;

    protected $table = 'postbox';
    protected $primaryKey = 'id';

    public function postCategoryInfo() {
        return $this->belongsTo('App\Models\Categories', 'category_id', 'id');
    }
}
