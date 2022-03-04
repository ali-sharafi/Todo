<?php

namespace Todo;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\ServiceProvider;

class TodoServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        JsonResource::withoutWrapping();
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerMigrations();
        $this->registerFactories();
    }

    public function registerFactories()
    {
        $this->loadFactoriesFrom(__DIR__ . '/Factories');
    }

    protected function registerMigrations()
    {
        return $this->loadMigrationsFrom(__DIR__ . '/Migrations');
    }
}
