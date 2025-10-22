<?php

namespace Kathenae\SSG;

use RuntimeException;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class Data
{

    private static string $dataDir = 'data';

    public static function useDirectory(string $dir)
    {
        self::$dataDir = $dir;
    }

    public static function load(string $collection)
    {
        try {
            return new DataCollection(Yaml::parseFile(self::$dataDir . DIRECTORY_SEPARATOR . "$collection.yml"));
        } catch (ParseException $exception) {
            throw new RuntimeException("Failed to load collection: $collection");
        }
    }
}