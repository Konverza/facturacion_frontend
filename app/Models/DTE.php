<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Business;

class DTE extends Model
{
    protected $table = 'dtes';

    protected $fillable = [
        "business_id",
        "user_id",
        "content",
        "name",
        "type",
        "status",
        "error_message"
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
