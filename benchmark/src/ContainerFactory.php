<?php

declare(strict_types=1);

namespace Takaram\NanoDi\Benchmark;

use DI\Container as PhpDiContainer;
use DI\ContainerBuilder as PhpDiContainerBuilder;
use League\Container\Container as LeagueContainer;
use League\Container\ReflectionContainer;
use Takaram\NanoDi\Benchmark\Fixture\ApiKernel;
use Takaram\NanoDi\Benchmark\Fixture\ClockInterface;
use Takaram\NanoDi\Benchmark\Fixture\EventBus;
use Takaram\NanoDi\Benchmark\Fixture\EventBusInterface;
use Takaram\NanoDi\Benchmark\Fixture\JsonSerializer;
use Takaram\NanoDi\Benchmark\Fixture\LoggerInterface;
use Takaram\NanoDi\Benchmark\Fixture\NullLogger;
use Takaram\NanoDi\Benchmark\Fixture\PaymentGateway;
use Takaram\NanoDi\Benchmark\Fixture\PaymentGatewayInterface;
use Takaram\NanoDi\Benchmark\Fixture\SerializerInterface;
use Takaram\NanoDi\Benchmark\Fixture\SystemClock;
use Takaram\NanoDi\Benchmark\Fixture\UserRepository;
use Takaram\NanoDi\Benchmark\Fixture\UserRepositoryInterface;
use Takaram\NanoDi\Container as NanoDiContainer;
use function DI\autowire;

final class ContainerFactory
{
    public static function targetClass(): string
    {
        return ApiKernel::class;
    }

    public static function nanoDi(): NanoDiContainer
    {
        return new NanoDiContainer(self::interfaceMap());
    }

    public static function phpDi(): PhpDiContainer
    {
        $builder = new PhpDiContainerBuilder();
        $builder->addDefinitions([
            ClockInterface::class => autowire(SystemClock::class),
            LoggerInterface::class => autowire(NullLogger::class),
            SerializerInterface::class => autowire(JsonSerializer::class),
            PaymentGatewayInterface::class => autowire(PaymentGateway::class),
            EventBusInterface::class => autowire(EventBus::class),
            UserRepositoryInterface::class => autowire(UserRepository::class),
        ]);

        return $builder->build();
    }

    public static function leagueContainer(): LeagueContainer
    {
        $container = new LeagueContainer();
        foreach (self::interfaceMap() as $id => $concrete) {
            $container->add($id, $concrete);
        }

        $container->delegate(new ReflectionContainer(cacheResolutions: false));

        return $container;
    }

    /**
     * @return array<string, string>
     */
    private static function interfaceMap(): array
    {
        return [
            ClockInterface::class => SystemClock::class,
            LoggerInterface::class => NullLogger::class,
            SerializerInterface::class => JsonSerializer::class,
            PaymentGatewayInterface::class => PaymentGateway::class,
            EventBusInterface::class => EventBus::class,
            UserRepositoryInterface::class => UserRepository::class,
        ];
    }
}
