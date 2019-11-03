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
        'id','name',
    ];

    protected $hidden = ['pivot'];

    public function users()
    {
        return $this->belongsToMany('App\User', 'user_role');
    }

}
