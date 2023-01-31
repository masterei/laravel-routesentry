<?php

namespace Masterei\Sentry\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Masterei\Sentry\General\Config;

class ResourceLinkCommand extends Command
{
    protected $signature = 'sentry:link';

    protected $description = 'Linking resources into application public folder';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $target_folder = __DIR__ . '/../../public';
        $link_folder = base_path('public\\assets\\' . Config::get('route_prefix'));
        File::ensureDirectoryExists(base_path('public/assets'));

        symlink($target_folder, $link_folder);

        $this->info($link_folder);
        $this->info('Sentry assets link has been created.');
    }
}
