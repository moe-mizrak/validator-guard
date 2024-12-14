<?php

namespace MoeMizrak\ValidatorGuard;

use Illuminate\Support\ServiceProvider;
use MoeMizrak\ValidatorGuardCore\ValidatorGuardCoreServiceProvider;

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

        $this->configureValidatorGuardCore();
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

    /**
     * Set validator-guard configuration options to validator-guard-core.
     *
     * @return void
     */
    protected function configureValidatorGuardCore(): void
    {
        if (! config('validator-guard-core')) {
            config(['validator-guard-core.attributes.before' => config('validator-guard.attributes.before')]);
            config(['validator-guard-core.attributes.after' => config('validator-guard.attributes.after')]);

            config(['validator-guard-core.class_list' => config('validator-guard.class_list')]);
        }

        // Register ValidatorGuardCoreServiceProvider
        $this->app->register(ValidatorGuardCoreServiceProvider::class);
    }
}