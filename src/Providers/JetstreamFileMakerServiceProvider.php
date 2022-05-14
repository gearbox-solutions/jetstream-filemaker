<?php

namespace BlueFeather\JetstreamFileMaker\Providers;


use App\Models\User;
use BlueFeather\JetstreamFileMaker\Console\InstallCommand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;

class JetstreamFileMakerServiceProvider extends ServiceProvider
{

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        $this->configureCommands();

        Auth::provider('filemaker', function ($app, array $config) {
            // Return an instance of Illuminate\Contracts\Auth\UserProvider...

            return new FileMakerUserProvider($this->app['hash'], $config['model']);
        });


        // Set the query for authentication since we need to use == for FileMaker
        Fortify::authenticateUsing(function (Request $request) {
            $user = User::where('email', "==", $request->email)->first();

            if ($user &&
                Hash::check($request->password, $user->password)) {
                return $user;
            }
        });
    }

    /**
     * Configure the commands offered by the application.
     *
     * @return void
     */
    protected function configureCommands()
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->commands([
            InstallCommand::class,
        ]);
    }
}