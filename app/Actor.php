<?php

namespace App;


use Illuminate\Database\Eloquent\Model;

class Actor extends Model
{

    protected $table = 'actors';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id','name','surname','born','info',
    ];

    protected $hidden = [
        'pivot',
    ];



}
