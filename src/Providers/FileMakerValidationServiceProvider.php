<?php

namespace GearboxSolutions\JetstreamFileMaker\Providers;


use GearboxSolutions\JetstreamFileMaker\Validation\DatabasePresenceVerifier;
use Illuminate\Validation\ValidationServiceProvider;

class FileMakerValidationServiceProvider extends ValidationServiceProvider
{

    /**
     * Register the database presence verifier.
     *
     * @return void
     */
    protected function registerPresenceVerifier()
    {
        $this->app->singleton('validation.presence', function ($app) {
            return new DatabasePresenceVerifier($app['db']);
        });
    }

}
