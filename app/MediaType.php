<?php

namespace App;


use Illuminate\Database\Eloquent\Model;

class MediaType extends Model
{

    protected $table = 'media_types';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    protected $hidden = ['pivot'];



}
