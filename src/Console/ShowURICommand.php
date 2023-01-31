<?php

namespace Masterei\Sentry\Console;

use Illuminate\Console\Command;
use Masterei\Sentry\Sentry;

class ShowURICommand extends Command
{
    protected $signature = 'sentry:show-uri';

    protected $description = 'Show URI permissions';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $permissions = Sentry::get();

        if($permissions->isNotEmpty()){
            $this->table([
                'URI',
                'Method',
                'Guard',
                'Group',
                'RouteName'
            ], $permissions->map(function ($value){
                return [
                    $value->uri,
                    $value->method,
                    $value->guard_name,
                    $value->group,
                    $value->route_name
                ];
            })->toArray());
        } else {
            $this->error('No URI permissions found.');
        }
    }
}
