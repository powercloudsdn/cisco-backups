<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Backup extends Model
{
    use HasFactory;

    protected $fillable = [
        "uuid",
        "device_id",
        "path_to_s3",
    ];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}
