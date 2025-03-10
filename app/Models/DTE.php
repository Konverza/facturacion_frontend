<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DTE extends Model
{
    protected $table = 'dtes';

    protected $fillable = [
        "business_id",
        "content",
        "type",
        "status",
        "error_message"
    ];
}
