<?php

namespace App\Console\Commands;

use App\Jobs\BackupCreate;
use App\Models\Device;
use Illuminate\Console\Command;

class BackupDevices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:devices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $devices  = Device::get();

        foreach ($devices as $device) {
            BackupCreate::dispatch($device);
        }

        return Command::SUCCESS;
    }
}
