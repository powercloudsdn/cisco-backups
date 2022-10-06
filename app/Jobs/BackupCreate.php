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
use Illuminate\Support\Facades\Blade as FacadesBlade;

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
        $ftp_ip = config('app.app_ip');

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

        $tmpfname = "/home/keegan/Documents/cisco-backups/backup";//tempnam(sys_get_temp_dir(), "");

        //rename($tmpfname, $tmpfname .= '.bash');

        $handle = fopen($tmpfname, "w");
        fwrite($handle, $script);
        fclose($handle);
        // chmod($tmpfname, 755);

        passthru("expect $tmpfname", $output);

        dd($output);

        sleep(2);

        $dir = "tftp/$filename";

        if (!file_exists($dir)) {
            $this->release();
            return;
        }

        $file_contents = file_get_contents($dir);

        $backup_file = Backup::where(["device_id" => $device->id])->latest()->first();

        $newest_backup_file = Storage::disk('local')->get($backup_file->path_to_s3);

        if (sha1($file_contents) === sha1($newest_backup_file)) {
            return;
        }

        Storage::disk('local')->put("$filename", $file_contents);

        Backup::create([
            "device_id" => $device->id,
            "path_to_s3" => $filename
        ]);
    }
}
