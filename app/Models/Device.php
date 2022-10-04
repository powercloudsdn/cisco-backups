<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory;

    protected $fillable = [
        "username",
        "password",
        "ip_address",
        "name",
        "group_id",
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}
