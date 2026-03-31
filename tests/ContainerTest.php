<?php

declare(strict_types=1);

namespace Takaram\NanoDi\Tests;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use PHPUnit\Framework\TestCase;
use Takaram\NanoDi\Container;
use Takaram\NanoDi\Tests\Fixture\TestCircularDependencyA;
use Takaram\NanoDi\Tests\Fixture\TestConsumer;
use Takaram\NanoDi\Tests\Fixture\TestDependency;
use Takaram\NanoDi\Tests\Fixture\TestExplodingService;
use Takaram\NanoDi\Tests\Fixture\TestOptionalScalarConsumer;
use Takaram\NanoDi\Tests\Fixture\TestPlainObject;
use Takaram\NanoDi\Tests\Fixture\TestScalarConsumer;
use Takaram\NanoDi\Tests\Fixture\TestService;
use Takaram\NanoDi\Tests\Fixture\TestServiceContract;
use Throwable;

final class ContainerTest extends TestCase
{
    public function testHasReturnsTrueForMappedId(): void
    {
        $container = new Container([
            TestServiceContract::class => TestService::class,
        ]);

        $this->assertTrue($container->has(TestServiceContract::class));
    }

    public function testHasReturnsTrueForExistingClassWithoutMap(): void
    {
        $container = new Container();

        $this->assertTrue($container->has(TestDependency::class));
    }

    public function testHasReturnsFalseForUnknownId(): void
    {
        $container = new Container();

        $this->assertFalse($container->has('Takaram\\NanoDi\\Tests\\Fixture\\UnknownClass'));
    }

    public function testHasReturnsFalseForNonInstantiableEntryWithoutMap(): void
    {
        $container = new Container();

        $this->assertFalse($container->has(TestServiceContract::class));
    }

    public function testHasReturnsFalseForMappedUnknownClass(): void
    {
        $container = new Container([
            TestServiceContract::class => 'Takaram\\NanoDi\\Tests\\Fixture\\UnknownClass',
        ]);

        $this->assertFalse($container->has(TestServiceContract::class));
    }

    public function testHasReturnsFalseForSelfReferencingMap(): void
    {
        $container = new Container([
            'service' => 'service',
        ]);

        $this->assertFalse($container->has('service'));
    }

    public function testHasReturnsFalseForCircularMap(): void
    {
        $container = new Container([
            'service.a' => 'service.b',
            'service.b' => 'service.a',
        ]);

        $this->assertFalse($container->has('service.a'));
    }

    public function testGetResolvesMappedClass(): void
    {
        $container = new Container([
            TestServiceContract::class => TestService::class,
        ]);

        $service = $container->get(TestServiceContract::class);

        $this->assertInstanceOf(TestService::class, $service);
    }

    public function testGetResolvesConstructorDependenciesRecursively(): void
    {
        $container = new Container([
            TestServiceContract::class => TestService::class,
        ]);

        $consumer = $container->get(TestConsumer::class);

        $this->assertInstanceOf(TestConsumer::class, $consumer);
        $this->assertInstanceOf(TestService::class, $consumer->service);
        $this->assertInstanceOf(TestDependency::class, $consumer->service->dependency);
    }

    public function testGetThrowsNotFoundExceptionForUnknownId(): void
    {
        $container = new Container();

        $this->expectException(NotFoundExceptionInterface::class);

        $container->get('Takaram\\NanoDi\\Tests\\Fixture\\UnknownClass');
    }

    public function testGetThrowsContainerExceptionWhenDependencyCannotBeResolved(): void
    {
        $container = new Container();

        $this->expectException(ContainerExceptionInterface::class);

        $container->get(TestConsumer::class);
    }

    public function testGetResolvesClassWithoutConstructor(): void
    {
        $container = new Container();

        $service = $container->get(TestPlainObject::class);

        $this->assertInstanceOf(TestPlainObject::class, $service);
    }

    public function testGetThrowsContainerExceptionForCircularDependency(): void
    {
        $container = new Container();

        try {
            $container->get(TestCircularDependencyA::class);
            $this->fail('Expected a container exception for circular dependency.');
        } catch (Throwable $e) {
            $this->assertInstanceOf(ContainerExceptionInterface::class, $e);
            $this->assertStringContainsString('Circular dependency detected', $this->exceptionMessages($e));
        }
    }

    public function testGetThrowsContainerExceptionForInvalidMappedClass(): void
    {
        $container = new Container([
            TestServiceContract::class => 'Takaram\\NanoDi\\Tests\\Fixture\\UnknownClass',
        ]);

        $this->expectException(ContainerExceptionInterface::class);

        $container->get(TestServiceContract::class);
    }

    public function testGetThrowsContainerExceptionForNonInstantiableEntry(): void
    {
        $container = new Container();

        $this->expectException(ContainerExceptionInterface::class);

        $container->get(TestServiceContract::class);
    }

    public function testGetUsesDefaultValueForBuiltinParameter(): void
    {
        $container = new Container();

        $consumer = $container->get(TestOptionalScalarConsumer::class);

        $this->assertSame('default', $consumer->name);
    }

    public function testGetThrowsContainerExceptionForBuiltinParameterWithoutDefault(): void
    {
        $container = new Container();

        $this->expectException(ContainerExceptionInterface::class);

        $container->get(TestScalarConsumer::class);
    }

    public function testGetWrapsInstantiationFailureInContainerException(): void
    {
        $container = new Container();

        $this->expectException(ContainerExceptionInterface::class);

        $container->get(TestExplodingService::class);
    }

    private function exceptionMessages(Throwable $e): string
    {
        $messages = [];

        do {
            $messages[] = $e->getMessage();
            $e = $e->getPrevious();
        } while ($e !== null);

        return implode(' | ', $messages);
    }
}
