<?php

namespace Masterei\Sentry\Console;

use Illuminate\Console\Command;
use Masterei\Sentry\General\Config;
use Masterei\Sentry\General\Install;

class InstallCommand extends Command
{
    protected $signature = 'sentry:install';

    protected $description = 'Setup startup data';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->info('Creating default roles.');
        Install::defaultRoles();
        $this->info('<fg=yellow;>Roles: ' . implode(', ', Config::get('administrative_access')) . '</>');
    }
}
