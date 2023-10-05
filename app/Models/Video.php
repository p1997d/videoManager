<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    protected $table = 'video';
    protected $quarde = false;
    protected $guarded = [];
    protected $keyType = 'string';
    public $incrementing = false;
}
