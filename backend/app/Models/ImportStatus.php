<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportStatus extends Model
{
    protected $fillable = ['import_id','status'];
}
