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
        return $this->belongsToMany('App\MediaFile', 'media_files');
    }
}
