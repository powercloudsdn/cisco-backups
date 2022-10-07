<?php

namespace App\Jobs;

use App\Models\Backup;
use App\Models\Device;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use App\Helpers\Blade;
use App\Models\BackupLog;
use Exception;
use Illuminate\Support\Facades\Blade as FacadesBlade;
use Illuminate\Support\Str;

class BackupCreate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public Device $device)
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $device = $this->device;

        $device_uuid = $device->uuid;
        $ftp_ip = config('tftp.ip');

        $filename = $device_uuid . "-" . time();

        $device_command = $device->backup_script->command ?? null;

        if (!$device_command) {
            return;
        }

        $variables = [
            "username" => $device->username,
            "password" => $device->password,
            "enable_password" => $device->enable_password,
            "ip_address" => $device->ip_address,
            "tftp_ip_address" => $ftp_ip,
            "filename" => $filename,
        ];

        $php = FacadesBlade::compileString($device_command);
        $script = Blade::render($php, $variables);

        $file_name = Str::uuid();

        Storage::disk("cisco_backups")->put($file_name, $script);

        $file_url = Storage::path($file_name);

        exec("expect $file_url", $output);

        $backup = BackupLog::create([
            "log" => implode("\n", $output),
            "device_id" => $device->id,
            "status" => "Pending",
        ]);

        Storage::disk("cisco_backups")->delete($file_name);

        SyncBackupToStorage::dispatch($device, $backup, $filename)->delay(now()->addSeconds(10));
    }
}
