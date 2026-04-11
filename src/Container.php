<?php

declare(strict_types=1);

namespace Takaram\NanoDi;

use Psr\Container\ContainerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionParameter;
use Takaram\NanoDi\Exception\ContainerException;
use Takaram\NanoDi\Exception\NotFoundException;
use Throwable;

class Container implements ContainerInterface
{
    /**
     * @var array<string, object>
     */
    private array $instances = [];

    /**
     * @param array<string, string> $map
     */
    public function __construct(private array $map = []) {}

    public function has(string $id): bool
    {
        return $this->canResolve($id, []);
    }

    /**
     * @template T of object
     *
     * @param class-string<T>|string $id
     * @return ($id is class-string<T> ? T : mixed)
     */
    public function get(string $id): mixed
    {
        return $this->resolve($id, []);
    }

    /**
     * @param list<string> $resolving
     */
    private function resolve(string $id, array $resolving): mixed
    {
        if (isset($this->instances[$id])) {
            return $this->instances[$id];
        }

        if (in_array($id, $resolving, true)) {
            throw new ContainerException(sprintf('Circular dependency detected while resolving "%s".', $id));
        }

        $resolving[] = $id;

        if (isset($this->map[$id])) {
            try {
                $instance = $this->resolve($this->map[$id], $resolving);
                if (is_object($instance)) {
                    $this->instances[$id] = $instance;
                }

                return $instance;
            } catch (NotFoundExceptionInterface $e) {
                throw new ContainerException(
                    sprintf('Unable to resolve entry "%s" because mapped class "%s" was not found.', $id, $this->map[$id]),
                    0,
                    $e,
                );
            }
        }

        if (!class_exists($id) && !interface_exists($id)) {
            throw new NotFoundException(sprintf('No entry found for "%s".', $id));
        }

        $reflectionClass = new ReflectionClass($id);

        if (!$reflectionClass->isInstantiable()) {
            throw new ContainerException(sprintf('Entry "%s" is not instantiable.', $id));
        }

        $constructor = $reflectionClass->getConstructor();
        if ($constructor === null) {
            return $this->instances[$id] = $this->instantiate($id);
        }

        $args = [];
        foreach ($constructor->getParameters() as $param) {
            $args[] = $this->resolveParameter($id, $param, $resolving);
        }

        return $this->instances[$id] = $this->instantiate($id, $args);
    }

    /**
     * @param list<string> $resolving
     */
    private function canResolve(string $id, array $resolving): bool
    {
        if (in_array($id, $resolving, true)) {
            return false;
        }

        $resolving[] = $id;

        if (isset($this->map[$id])) {
            return $this->canResolve($this->map[$id], $resolving);
        }

        if (!class_exists($id) && !interface_exists($id)) {
            return false;
        }

        $reflectionClass = new ReflectionClass($id);

        return $reflectionClass->isInstantiable();
    }

    /**
     * @param list<string> $resolving
     */
    private function resolveParameter(string $id, ReflectionParameter $parameter, array $resolving): mixed
    {
        $type = $parameter->getType();

        if ($type instanceof ReflectionNamedType && !$type->isBuiltin()) {
            try {
                return $this->resolve($type->getName(), $resolving);
            } catch (ContainerExceptionInterface $e) {
                throw new ContainerException(
                    sprintf('Unable to resolve parameter "$%s" of "%s".', $parameter->getName(), $id),
                    0,
                    $e,
                );
            }
        }

        if ($parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        }

        throw new ContainerException(sprintf('Unable to resolve parameter "$%s" of "%s".', $parameter->getName(), $id));
    }

    /**
     * @param list<mixed> $args
     */
    private function instantiate(string $id, array $args = []): object
    {
        try {
            return new $id(...$args);
        } catch (Throwable $e) {
            throw new ContainerException(sprintf('Failed to instantiate "%s".', $id), 0, $e);
        }
    }
}
