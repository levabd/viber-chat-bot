<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Viber\Bot as ViberBot;

class ViberBotServiceProvider extends ServiceProvider
{

    protected $defer = true;

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('viber_bot', function ($app) {
            return (new ViberBot([
                'token' => config('viber.api_key')
            ]))->getClient();
        });
    }

    public function provides()
    {
        return [
            'viber_bot'
        ];
    }
}
