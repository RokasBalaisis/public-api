<?php

namespace App;


use Illuminate\Database\Eloquent\Model;
use App\Category;
use App\Media;

class MediaType extends Model
{

    protected $table = 'media_types';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id','name',
    ];

    protected $hidden = ['pivot', 'laravel_through_key'];


    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function media()
    {
        return $this->hasManyThrough(Media::class, Category::class);
    }

}
