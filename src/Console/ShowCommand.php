<?php

namespace Masterei\Sentry\Console;

use Spatie\Permission\Commands\Show;

class ShowCommand extends Show
{
    protected $signature = 'sentry:show
            {guard? : The name of the guard}
            {style? : The display style (default|borderless|compact|box)}';

    protected $description = 'Show table of roles and uri permissions';

    public function __construct()
    {
        parent::__construct();
    }
}
