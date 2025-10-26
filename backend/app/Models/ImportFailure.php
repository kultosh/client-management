<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportFailure extends Model
{
    protected $fillable = ['import_id', 'row', 'attribute', 'errors', 'values'];
    protected $casts = ['errors' => 'array', 'values' => 'array'];
}
