<?php

namespace Masterei\Sentry\Publish;

use Illuminate\Support\Facades\File;

class ConfigPublish
{
    public static function config()
    {
        foreach (File::allFiles(__DIR__ .'/../../config') as $file){
            if(!File::exists(base_path("config\\") . $file->getFilename())){
                File::copy($file->getRealPath(), base_path("config\\" . $file->getFilename()));
            }
        }
    }
}
