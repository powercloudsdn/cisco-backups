<?php

namespace App\Models;

use App\Casts\PasswordCast;
use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory;
    use Uuid;

    protected $fillable = [
        "username",
        "password",
        "enable_password",
        "ip_address",
        "name",
        "group_id",
    ];

    protected $casts = [
        "password" => PasswordCast::class,
        "enable_password" => PasswordCast::class
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function backup_script()
    {
        return $this->belongsTo(BackupScript::class);
    }

    public function backups()
    {
        return $this->hasMany(Backup::class);
    }

    public function backup_log()
    {
        return $this->hasMany(BackupLog::class);
    }
}
