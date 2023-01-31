<?php

namespace Masterei\Sentry\Console;

use Illuminate\Console\Command;
use Masterei\Sentry\Publish\ConfigPublish;
use Masterei\Sentry\Publish\MigrationPublish;

class PublishCommand extends Command
{
    protected $signature = 'sentry:publish';

    protected $description = 'Publish default package files';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        ConfigPublish::config();
        $this->info('Sentry config has been successfully published.');

        MigrationPublish::dependencyMigration();
        MigrationPublish::sentryMigration();
        $this->info('Sentry migrations has been successfully published.');
    }
}
