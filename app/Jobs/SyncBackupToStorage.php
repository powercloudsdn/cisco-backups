<?php

namespace App\Jobs;

use App\Models\Backup;
use App\Models\BackupLog;
use App\Models\Device;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Laravel\Nova\Notifications\NovaNotification;
use Laravel\Nova\URL;

class SyncBackupToStorage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public Device $device, public BackupLog $backup, public $filename)
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $filename = $this->filename;
        $device = $this->device;
        $backup = $this->backup;

        $dir = config('tftp.path') . $filename;

        Log::info("fetching file $dir");
        
        Log::info(file_exists($dir));

        if (!file_exists($dir)) {
            $users = User::get();

            foreach ($users as $user ) {
                $user->notify(
                    NovaNotification::make()
                        ->message('A backup has failed.')
                        ->action('Go To Log', URL::remote('/nova/resources/backup-logs/' . $backup->id))
                        ->icon('error')
                        ->type('error'));
            }

            BackupLog::where(["id" => $backup->id])->update([
                "status" => "Failed",
            ]);
            Log::info("failed to fetch file");

            $this->fail();

            return;
        }

        $file_contents = file_get_contents($dir);

        $backup_file = Backup::where(["device_id" => $device->id])->latest()->first();

        $path_to_s3 = $backup_file->path_to_s3 ?? null;

        if ($path_to_s3) {
            $newest_backup_file = Storage::disk('cisco_backups')->get($path_to_s3);
        } else {
            $newest_backup_file = "";
        }

        if (sha1($file_contents) === sha1($newest_backup_file)) {
            BackupLog::where(["id" => $backup->id])->update([
                "status" => "Success",
            ]);
            return;
        }

        Storage::disk('cisco_backups')->put("$filename", $file_contents);

        BackupLog::where(["id" => $backup->id])->update([
            "status" => "Success",
        ]);

        Backup::create([
            "device_id" => $device->id,
            "path_to_s3" => $filename
        ]);

        return;
    }
}
