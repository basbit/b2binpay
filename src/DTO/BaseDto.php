<?php

namespace B2Binpay\DTO;

use DateTime;
use ReflectionClass;
use ReflectionProperty;

class BaseDto
{
    protected static $instance;

    public function __construct($values = [])
    {
        self::$instance = $this;

        if (is_object($values) && method_exists($values, 'toArray')) {
            $values = $values->toArray();
        }

        if (is_array($values)) {
            $this->setFromArray($values);
        }
    }

    public static function setFromArray(array $properties, bool $isCamelize = true): self
    {
        $reflection = new ReflectionClass(self::$instance);

        foreach ($properties as $property => $value) {
            if ($isCamelize) {
                $property = self::camelize($property);
            }

            if ($reflection->hasProperty($property)) {
                $property = $reflection->getProperty($property);
                if ($property instanceof ReflectionProperty) {
                    if ($property->getModifiers() !== 'public') {
                        $property->setAccessible(true);
                    }

                    $property->setValue(self::$instance, self::setWithType($property, $value));
                }
            }
        }

        return self::$instance;
    }

    public static function setWithType(ReflectionProperty $property, $value)
    {
        switch ($property->getType()->getName()) {
            case DateTime::class:
                $value = new DateTime($value);
                break;
            case 'string':
                $value = (string)$value;
                break;
            case 'int':
                $value = (int)$value;
                break;
            case 'float':
                $value = (float)$value;
                break;
            case 'bool':
                $value = (bool)$value;
                break;
            case 'array':
                $value = (array)$value;
                break;
            /*default:
                $className = $property->getType()->getName();
                if(class_exists($className)) {
                    $reflection = new ReflectionClass($className);
                    $class = $reflection->newInstanceWithoutConstructor();

                    if($class instanceof BaseDto) {
                        $value = $class::setFromArray($value);
                    }
                }*/
        }

        return $value;
    }

    public function toArray(bool $isRecursive = true): array
    {
        $result = [];

        foreach (get_object_vars($this) as $propertyName => $propertyValue) {
            if (is_null($propertyValue) || (is_array($propertyValue) && !$propertyValue)) {
                continue;
            }

            if ($isRecursive && is_object($propertyValue)) {
                $propertyValue = $propertyValue->toArray();
            }

            $result[$propertyName] = $propertyValue;
        }

        return self::cutCommonData($result);
    }

    public function toJson(): string
    {
        return json_encode($this->toArray());
    }

    public function toArrayWithSnakeKeys(bool $isRecursive = true): array
    {
        $result = [];

        foreach (get_object_vars($this) as $propertyName => $propertyValue) {
            if (is_null($propertyValue) || (is_array($propertyValue) && !$propertyValue)) {
                continue;
            }

            if ($isRecursive && is_object($propertyValue)) {
                $propertyValue = $propertyValue->toArrayWithSnakeKeys();
            }

            $propertyName = self::decamelize($propertyName);
            $result[$propertyName] = $propertyValue;
        }

        return self::cutCommonData($result);
    }

    protected static function cutCommonData(array $data): array
    {
        $cutArray = [
            '__initializer__',
            '__cloner__',
            '__isInitialized__',
        ];

        foreach ($data as $key => $value) {
            if (empty($value) && in_array($key, $cutArray, true)) {
                unset($data[$key]);
            }
        }

        return $data;
    }

    private static function camelize(?string $string): string
    {
        $delimiter = strpos($string, '-') !== false ? '-' : '_';

        $words = explode($delimiter, $string);
        $words = array_map('ucfirst', $words);
        return lcfirst(implode('', $words));
    }

    private static function decamelize(?string $string): string
    {
        return strtolower(preg_replace(['/([a-z\d])([A-Z])/', '/([^_])([A-Z][a-z])/'], '$1_$2', $string));
    }
}
