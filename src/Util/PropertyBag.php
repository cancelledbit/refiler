<?php


namespace Refiler\Util;


class PropertyBag
{
    private array $properties;

    public function __construct(?array $properties = [])
    {
        if ($properties === null) {
            $properties = [];
        }
        $this->properties = $properties;
    }

    public function getProperty(string $key, $default = null) {
        if (array_key_exists($key, $this->properties)) {
            return $this->properties[$key];
        }
        return $default;
    }
}