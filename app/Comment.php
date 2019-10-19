<?php

namespace App;


use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{

    protected $table = 'comments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'media_id', 'user_id', 'text',
    ];

    protected $hidden = ['pivot'];



}
