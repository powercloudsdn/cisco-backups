# Setup TFTP
    sudo apt update
    sudo apt install tftpd-hpa
    sudo systemctl status tftpd-hpa

<p>To configure the the ftp server </p>
    sudo nano /etc/default/tftpd-hpa

    TFTP_USERNAME="tftp"
    TFTP_DIRECTORY="your_dir/tftp"
    TFTP_ADDRESS="0.0.0.0:69"
    TFTP_OPTIONS="--secure"

Use the directory proveded in the laravel application
Change the owner of the group
    sudo chown tftp:tftp /tftp
    sudo systemctl restart tftpd-hpa


# Setup Expect
    sudo apt install expect

# Setup Supervisor

## Setup Supervisor
    sudo apt-get install supervisor

## Create Configuration
    cd /etc/supervisor/conf.d
    sudo nano cisco-backup.conf

## Config Contents                                               
    [program:laravel-worker]
    process_name=%(program_name)s_%(process_num)02d
    command=php /home/keegan/Documents/cisco-backups/artisan --tries=3 --daemon
    autostart=true
    autorestart=true
    user=keegan
    numprocs=1
    redirect_stderr=true
    stdout_logfile=/home/keegan/Documents/cisco-backups/storage/worker.log​


<p> Save the configration be sure to change user, command and stdout_logfile</p>

## Start Supervisor

    sudo supervisorctl reread

    sudo supervisorctl update

    sudo supervisorctl start laravel-worker:*

    sudo supervisorctl status​

<p> if you find any errors</p>
    sudo service supervisor reload

## Laravel .env File 

    QUEUE_CONNECTION=database

## Laravel Commands
    php artisan optimize
    php artisan queue:table 
    php artisan migrate
