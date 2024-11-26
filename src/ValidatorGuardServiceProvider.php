<?php

namespace MoeMizrak\ValidatorGuard;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use MoeMizrak\ValidatorGuard\Facades\ValidatorGuard;

class ValidatorGuardServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->registerPublishing();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->configure();

        $this->app->bind('validator-guard', function () {
            return new ValidatorGuard();
        });

        $this->app->bind(ValidatorGuard::class, function () {
            return $this->app->make('validator-guard');
        });

        // Register the facade alias.
        AliasLoader::getInstance()->alias('ValidatorGuard', ValidatorGuard::class);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides(): array
    {
        return ['validator-guard'];
    }

    /**
     * Setup the configuration.
     *
     * @return void
     */
    protected function configure(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/validator-guard.php', 'validator-guard'
        );
    }

    /**
     * Register the package's publishable resources.
     *
     * @return void
     */
    protected function registerPublishing(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/validator-guard.php' => config_path('validator-guard.php'),
            ], 'validator-guard');
        }
    }
}