<?php

namespace Jeylabs\Weather\Laravel;

use Illuminate\Contracts\Container\Container as Application;
use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Support\ServiceProvider;
use Jeylabs\Weather\Weather;
use Laravel\Lumen\Application as LumenApplication;

class WeatherServiceProvider extends ServiceProvider
{
    protected $defer = true;

    public function boot()
    {
        $this->setupConfig($this->app);
    }
    protected function setupConfig(Application $app)
    {
        $source = __DIR__ . '/config/weather.php';
        if ($app instanceof LaravelApplication && $app->runningInConsole()) {
            $this->publishes([$source => config_path('weather.php')]);
        }
        $this->mergeConfigFrom($source, 'weather');
    }

    public function register()
    {
        $this->registerBindings($this->app);
    }
    protected function registerBindings(Application $app)
    {
        $app->singleton('weather', function ($app) {
            $config = $app['config'];
            return new Weather(
                $config->get('weather.access_token', null)
            );
        });

        $app->alias('weather', Weather::class);
    }

    public function provides()
    {
        return ['weather'];
    }
}
