<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Articles extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $hidden = [
        'likes_counter',
        'views_counter',
        'tags_id',
        'full_text',
        'created_at', 
        'updated_at'
    ];

    public function tags()
    {
        return $this->belongsTo(Tags::class);
    }

    public function comments()
    {
        return $this->hasMany(Comments::class);
    }

}