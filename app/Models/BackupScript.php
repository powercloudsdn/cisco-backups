<?php

namespace App\Models;

use App\Casts\BackupScriptVariableCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BackupScript extends Model
{
    use HasFactory;

    protected $fillable = [
        "command",
        "name",
    ];

    public function device()
    {
        return $this->hasMany(Device::class);
    }
}
