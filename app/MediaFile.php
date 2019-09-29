<?php
namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

class MediaFile extends Pivot
{
    protected $table = 'media_files';
    public $incrementing = true;
    protected $dateFormat = 'Y-m-d H:i:s.u';
}