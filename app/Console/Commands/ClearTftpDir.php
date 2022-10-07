<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ClearTftpDir extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:tftp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clears the tftp folder';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $dir = scandir(config("tftp.path"));

        foreach($dir as $file){
            if ($file == "." || $file == "..") {
                continue;
            }

            $file = config("tftp.path") . "/". $file;

            if(is_file($file)) {
              unlink($file); 
            }
          }

        return Command::SUCCESS;
    }
}
