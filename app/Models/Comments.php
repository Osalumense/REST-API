<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comments extends Model
{
    protected $guarded = [];

    public function articles()
    {
        return $this->belongsTo(Articles::class);
    }

    public function rules()
    {
        return [
            'body' => 'required|string',
            'subject' => 'required|string'
        ];
    }
}