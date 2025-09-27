<?php

namespace Kathenae\SSG;

use RuntimeException;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class Data
{
    public static function load(string $collection)
    {
        try {
            return new DataCollection(Yaml::parseFile("data/$collection.yml"));
        } catch (ParseException $exception) {
            throw new RuntimeException("Failed to load collection: $collection");
        }
    }
}