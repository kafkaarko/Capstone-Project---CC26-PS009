<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    //
    protected $fillable = [
    'user_id',
    'image',
    'raw_text',
    'price_output',
    'datetime',
    'category'
];
public function user()
{
    return $this->belongsTo(User::class);
}
}
