<?php

namespace Masterei\Sentry;

use Illuminate\Support\ServiceProvider;
use Masterei\Sentry\Console\CacheCommand;
use Masterei\Sentry\Console\CreateRoleCommand;
use Masterei\Sentry\Console\InstallCommand;
use Masterei\Sentry\Console\PublishCommand;
use Masterei\Sentry\Console\ResourceLinkCommand;
use Masterei\Sentry\Console\ShowCommand;
use Masterei\Sentry\Console\ShowRoleCommand;
use Masterei\Sentry\Console\ShowURICommand;
use Masterei\Sentry\Console\TruncateDatabaseCommand;
use Illuminate\Routing\Router;

class SentryServiceProvider extends ServiceProvider
{
    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/sentry.php', 'sentry');

        // registering middleware
        $this->app->booted(function () {
            $router = $this->app->make(Router::class);

            $router->aliasMiddleware('sentry', SentryMiddleware::class);
        });
    }

    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishableFiles();
        $this->loadConsoleCommands();
    }

    protected function publishableFiles()
    {
        $this->publishes([
            __DIR__.'/../config/sentry.php' => config_path('sentry.php')
        ], 'sentry');
    }

    protected function loadConsoleCommands()
    {
        if (!$this->app->runningInConsole()) {
            return false;
        }

        $this->commands([
            CacheCommand::class,
            PublishCommand::class,
            InstallCommand::class,
            CreateRoleCommand::class,
            ShowCommand::class,
            ShowRoleCommand::class,
            ShowURICommand::class,
            ResourceLinkCommand::class,
            TruncateDatabaseCommand::class,

        ]);
    }
}
