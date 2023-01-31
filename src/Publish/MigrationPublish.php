<?php

namespace Masterei\Sentry\Publish;

use Illuminate\Support\Facades\File;

class MigrationPublish
{
    public static function dependencyMigration()
    {
        $path = base_path('vendor\\spatie\\laravel-permission\\database\\migrations\\create_permission_tables.php.stub');
        $filename = File::name($path);

        if(!self::isMigrationExist($filename)){
            self::createMigrationFile($path, $filename);
        }
    }

    public static function sentryMigration()
    {
        foreach (File::allFiles(__DIR__ .'/../../database/migrations') as $file){
            $filename = preg_replace('/^[0-9_]*/', '', $file->getFilename());

            if(!self::isMigrationExist($filename)){
                self::createMigrationFile($file->getRealPath(), $filename);
            }
        }
    }

    protected static function isMigrationExist($filename)
    {
        foreach (File::allFiles(base_path("database\\migrations")) as $cur_migration){
            if(str_contains($cur_migration->getFilename(), $filename)){
                return true;
            }
        }

        return false;
    }

    protected static function createMigrationFile($path, $filename)
    {
        File::copy($path, base_path("database\\migrations\\" . self::makeMigrationName($filename)));
    }

    protected static function makeMigrationName($base_name)
    {
        return now()->format('Y_m_d_His') . '_' . $base_name;
    }
}
