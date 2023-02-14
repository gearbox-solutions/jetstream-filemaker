<?php

namespace GearboxSolutions\JetstreamFileMaker\Console;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jetstream-filemaker:install';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the Jetstream-FileMaker resources';

    public function handle(){
        // Models...
        copy(__DIR__.'/../../stubs/app/Models/PasswordReset.php', app_path('Models/PasswordReset.php'));
        copy(__DIR__.'/../../stubs/app/Models/PersonalAccessToken.php', app_path('Models/PersonalAccessToken.php'));
        copy(__DIR__.'/../../stubs/app/Models/User.php', app_path('Models/User.php'));



    }
}
