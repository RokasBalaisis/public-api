<?php

namespace App;


use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{

    protected $table = 'ratings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'media_id', 'user_id', 'rating',
    ];

    protected $hidden = ['pivot'];



}
