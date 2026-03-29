<?php
declare(strict_types=1);

namespace Takaram\NanoDi;

use Psr\Container\ContainerInterface;
use ReflectionClass;

class Container implements ContainerInterface
{
    public function __construct(private array $map = [])
    {
    }

    public function has(string $id): bool
    {
        if (isset($this->map[$id])) {
            return true;
        }

        try {
            new ReflectionClass($id);
            return true;
        } catch (\ReflectionException) {
            return false;
        }
    }

    /**
     * @template T of object
     *
     * @param string $id
     * @return $className is class-string<T> ? T : mixed
     */
    public function get(string $id): mixed
    {
        if (isset($this->map[$id])) {
            return $this->get($this->map[$id]);
        }

        $reflectionClass = new ReflectionClass($id);
        $constructor = $reflectionClass->getConstructor();

        $args = [];
        foreach ($constructor->getParameters() as $param) {
            $parameterClass = $param->getType()->getName();
            $args[] = $this->get($parameterClass);
        }

        return new $id(...$args);
    }
}
