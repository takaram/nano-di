<?php
declare(strict_types=1);

namespace Takaram\NanoDi\Tests;

use PHPUnit\Framework\TestCase;
use Takaram\NanoDi\Container;
use Takaram\NanoDi\Tests\Fixture\TestConsumer;
use Takaram\NanoDi\Tests\Fixture\TestDependency;
use Takaram\NanoDi\Tests\Fixture\TestService;
use Takaram\NanoDi\Tests\Fixture\TestServiceContract;

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
}
