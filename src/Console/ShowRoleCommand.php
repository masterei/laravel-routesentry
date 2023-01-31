<?php

namespace Masterei\Sentry\Console;

use Illuminate\Console\Command;
use Masterei\Sentry\Sentry;

class ShowRoleCommand extends Command
{
    protected $signature = 'sentry:show-role';

    protected $description = 'Show role list';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $roles = Sentry::getRoles();

        if($roles->isNotEmpty()){
            $this->table(['Role', 'Guard'], $roles->map(function ($value){
                return [$value->name, $value->guard_name];
            })->toArray());
        } else {
            $this->error('No roles found.');
        }
    }
}
