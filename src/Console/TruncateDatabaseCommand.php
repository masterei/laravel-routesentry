<?php

namespace Masterei\Sentry\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Masterei\Sentry\General\Config;

class TruncateDatabaseCommand extends Command
{
    protected $signature = 'sentry:truncate';

    protected $description = 'Truncate all Sentry-related tables, including permissions, roles, and their relationships.';

    public function handle()
    {
        // package table
        DB::table(Config::get('table_name'))->truncate();

        // dependency package
        foreach(array_reverse(config('permission.table_names')) as $tableName) {
            DB::table($tableName)->delete();
        }

        $this->info('Sentry data has been truncated successfully.');
    }
}
