<?php

namespace Masterei\Sentry\Console;

use Illuminate\Console\Command;
use Masterei\Sentry\Sentry;

class CreateRoleCommand extends Command
{
    protected $signature = 'sentry:create-role {name}';

    protected $description = 'Create new role';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        Sentry::createRole(['name' => $this->argument('name')]);
        $this->info('New role has been successfully created.');
    }
}
