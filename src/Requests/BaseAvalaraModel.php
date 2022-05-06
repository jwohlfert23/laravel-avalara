<?php

namespace Jwohlfert23\LaravelAvalara\Requests;

use ReflectionClass;
use ReflectionProperty;

class BaseAvalaraModel
{
    public function toArray()
    {
        $data = [];

        $class = new ReflectionClass(static::class);

        $properties = $class->getProperties(ReflectionProperty::IS_PUBLIC);

        foreach ($properties as $property) {
            if ($property->isStatic()) {
                continue;
            }

            $value = $property->getValue($this);
            if ($value instanceof \DateTimeInterface) {
                $value = $value->format('Y-m-d');
            }
            if ($value instanceof \BackedEnum) {
                $value = $value->value;
            }
            $data[$property->getName()] = $value;
        }

        return $data;
    }
}
