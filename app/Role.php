<?php

namespace App;


use Illuminate\Database\Eloquent\Model;

class Role extends Model
{

    protected $table = 'roles';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name'
    ];

    public function users()
    {
        return $this->belongsToMany('App\User')->using('App\UserRole');
    }

}
