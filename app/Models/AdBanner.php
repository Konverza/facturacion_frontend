<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdBanner extends Model
{
    protected $fillable = ['name', 'image_path', 'link_url'];
}
