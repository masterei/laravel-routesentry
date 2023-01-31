<?php

namespace Masterei\Sentry\Console;

use Illuminate\Console\Command;
use Masterei\Sentry\General\Cache;

class CacheCommand extends Command
{
    protected $signature = 'sentry:cache';

    protected $description = 'Reset the permission cache';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $results = Cache::flushURIPermissions();

        $this->displayCreated($results['created']);
        $this->addNewLine($results['deleted']);
        $this->displayDeleted($results['deleted']);

        $this->addNewLine(array_merge($results['created'], $results['deleted']));
        Cache::flushDependencyPackageCache();
        Cache::flushPackageCache();
        Cache::ensurePackageDataConsistency();
        Cache::grantAllURIAccessToAdministrativeRoles();
        $this->info('Permission cache flushed.');
    }

    protected function displayCreated($data)
    {
        if($this->hasCount($data)){
            $this->info('Created');
            $this->table($this->tableFormat(), $data);
        }
    }

    protected function displayDeleted($data)
    {
        if($this->hasCount($data)){
            $this->error('Deleted');
            $this->table($this->tableFormat(), $data);
        }
    }

    protected function hasCount($data)
    {
        if(count($data) <= 0){
            return false;
        }

        return true;
    }

    protected function tableFormat()
    {
        return [
            'URI',
            'Method',
            'Guard',
            'Group',
            'RouteName'
        ];
    }

    protected function addNewLine($data)
    {
        // add only new line if data isn't null
        if($this->hasCount($data)){
            $this->newLine();
        }
    }
}
