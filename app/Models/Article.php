<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    public $timestamps = false;

    protected $table = 'articles';
    protected $fillable = [
        'title',
        'publication_date',
        'categories',
        'url'
    ];
}
