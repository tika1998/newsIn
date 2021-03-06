<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $fillable = [
       'news_id', 'name'
    ];

    public function news()
    {
        return $this->belongsTo('App\news', 'news_id');
    }
}
