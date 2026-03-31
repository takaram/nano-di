<?php
declare(strict_types=1);

namespace Takaram\NanoDi;

use Psr\Container\ContainerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionParameter;
use Takaram\NanoDi\Exception\ContainerException;
use Takaram\NanoDi\Exception\NotFoundException;
use Throwable;

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
        if (in_array($id, $resolving, true)) {
            throw new ContainerException(sprintf('Circular dependency detected while resolving "%s".', $id));
        }

        $resolving[] = $id;

        if (isset($this->map[$id])) {
            try {
                return $this->resolve($this->map[$id], $resolving);
            } catch (NotFoundExceptionInterface $e) {
                throw new ContainerException(
                    sprintf('Unable to resolve entry "%s" because mapped class "%s" was not found.', $id, $this->map[$id]),
                    0,
                    $e,
                );
            }
        }

        try {
            $reflectionClass = new ReflectionClass($id);
        } catch (ReflectionException $e) {
            throw new NotFoundException(sprintf('No entry found for "%s".', $id), 0, $e);
        }

        if (!$reflectionClass->isInstantiable()) {
            throw new ContainerException(sprintf('Entry "%s" is not instantiable.', $id));
        }

        $constructor = $reflectionClass->getConstructor();
        if ($constructor === null) {
            return $this->instantiate($reflectionClass->getName());
        }

        $args = [];
        foreach ($constructor->getParameters() as $param) {
            $args[] = $this->resolveParameter($id, $param, $resolving);
        }

        return $this->instantiate($reflectionClass->getName(), $args);
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

    private function instantiate(string $id, array $args = []): object
    {
        try {
            return new $id(...$args);
        } catch (Throwable $e) {
            throw new ContainerException(sprintf('Failed to instantiate "%s".', $id), 0, $e);
        }
    }
}
