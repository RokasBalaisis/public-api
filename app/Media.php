<?php

namespace App;


use Illuminate\Database\Eloquent\Model;

class Media extends Model
{

    protected $table = 'media';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'short_description', 'description', 'trailer_url'
    ];

        /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'pivot',
    ];

    public function files()
    {
        return $this->hasMany('App\MediaFile', 'media_id');
    }

    public function actors()
    {
        return $this->belongsToMany('App\Actor', 'media_actors');
    }
}
