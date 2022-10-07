<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BackupLog extends Model
{
    use HasFactory;

    protected $fillable = [
        "log",
        "device_id",
        "status"
    ];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}
