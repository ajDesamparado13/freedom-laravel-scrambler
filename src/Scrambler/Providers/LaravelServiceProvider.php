<?php

namespace Freedom\Scrambler\Providers;

use Illuminate\Support\ServiceProvider as ServiceProvider;
use Freedom\Scrambler\Impl\Scrambler;

class LaravelServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        \App::bind('scrambler',function(){
            return new Scrambler;
        });
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
    }
}
