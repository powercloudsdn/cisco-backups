<?php

namespace App\Console\Commands;

use App\Jobs\BackupCreate;
use App\Models\Device;
use Illuminate\Console\Command;
use App\Helpers\Blade;
use Illuminate\Support\Facades\Blade as FacadesBlade;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 't';

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
        $device  = Device::first();

        BackupCreate::dispatch($device);

        return Command::SUCCESS;
    }
}
