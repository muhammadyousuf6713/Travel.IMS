<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
//use Sofa\Eloquence\Eloquence;

class remarks extends Model
{

    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $primaryKey = 'id_remarks';
    protected $table = 'remarks';
    public $timestamps = true;
    protected $guarded = [];

    
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
