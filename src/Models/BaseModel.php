<?php

namespace Jwohlfert23\LaravelAvalara\Models;

use BackedEnum;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use ReflectionClass;
use ReflectionProperty;

class BaseModel implements \JsonSerializable, Arrayable
{
    public function getValue(ReflectionProperty $property, mixed $value): mixed
    {
        if (is_null($value)) {
            return null;
        }

        $type = $property->getType();
        $typeClass = $type instanceof \ReflectionNamedType ? $property->getType()->getName() : 'union';

        if (is_subclass_of($typeClass, BaseModel::class)) {
            return new $typeClass($value);
        }
        if (is_subclass_of($typeClass, DateTimeInterface::class) && ! $value instanceof DateTimeInterface) {
            return Carbon::parse($value);
        }
        if (is_subclass_of($typeClass, BackedEnum::class) && ! $value instanceof BackedEnum) {
            return $typeClass::from($value);
        }
        if (is_array($value)) {
            if (empty($value)) {
                return $value;
            }
            if (isset($value[0])) {
                return array_map(fn ($item) => $this->toNestedModel($property->getName(), $item), $value);
            }
        }

        return $value;
    }

    public function toNestedModel(string $name, array $value)
    {
        return null;
    }

    public function toValue(mixed $value): mixed
    {
        if (is_array($value)) {
            return array_map(fn ($item) => $this->toValue($item), $value);
        }
        if ($value instanceof BaseModel) {
            return $value->toArray();
        }
        if ($value instanceof DateTimeInterface) {
            return $value->format('Y-m-d');
        }
        if ($value instanceof BackedEnum) {
            return $value->value;
        }

        return $value;
    }

    public function dd()
    {
        Log::debug(static::class, $this->toArray());

        return dd($this->toArray());
    }

    public function __construct(...$args)
    {
        if (is_array($args[0] ?? null)) {
            $args = $args[0];
        }

        $class = new ReflectionClass(static::class);

        foreach ($class->getProperties() as $property) {
            if (Arr::has($args, $property->getName())) {
                $this->{$property->getName()} = $this->getValue($property, Arr::get($args, $property->getName()));
                Arr::forget($args, $property->name);
            }
        }
    }

    public function jsonSerialize(): array
    {
        $data = [];

        $class = new ReflectionClass(static::class);

        $properties = $class->getProperties(ReflectionProperty::IS_PUBLIC);

        foreach ($properties as $property) {
            if ($property->isStatic() || ! $property->isInitialized($this)) {
                continue;
            }

            $value = $property->getValue($this);

            if (is_null($value)) {
                continue;
            }

            $data[$property->getName()] = $this->toValue($value);
        }

        return $data;
    }

    public function only($keys)
    {
        return Arr::only($this->jsonSerialize(), is_array($keys) ? $keys : func_get_args());
    }

    public function except($keys)
    {
        return Arr::except($this->jsonSerialize(), is_array($keys) ? $keys : func_get_args());
    }

    public function toArray()
    {
        return $this->jsonSerialize();
    }
}
