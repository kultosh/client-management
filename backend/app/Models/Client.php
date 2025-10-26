<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;
    
    protected $fillable = ['company_name','email','phone_number','is_duplicate','duplicate_group_id'];

    public function scopeFilterByType($query, $type)
    {
        return match($type) {
            'duplicate' => $query->where('is_duplicate', true),
            'unique' => $query->where('is_duplicate', false),
            default => $query
        };
    }
}
