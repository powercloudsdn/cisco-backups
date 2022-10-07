<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BackupScriptSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('backup_scripts')->insert([
            'name' => "Default Cisco Script",
            'command' => '#!/usr/bin/expect -f

            set timeout -1
            
            spawn telnet {!! $ip_address !!}
            
            expect "Password: "
            
            send "{!! $password !!}\r"
            
            send "enable\r"
            
            expect "Password: "
            
            send "{!! $enable_password !!}\r"
            
            send "copy running-config tftp://{!! $tftp_ip_address !!}//{!! $filename !!}\r"
            
            expect "Address or name of remote host []?"
            
            send "{!! $tftp_ip_address !!}\r"
            
            expect "Destination filename ?"
            
            send "\r"
            send "exit\r"'
        ]);
    }
}
