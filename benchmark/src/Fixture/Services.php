<?php

declare(strict_types=1);

namespace Takaram\NanoDi\Benchmark\Fixture;

final class ApiKernel
{
    public function __construct(
        public readonly CheckoutAction $checkoutAction,
        public readonly HealthCheck $healthCheck,
        public readonly SerializerInterface $serializer,
    ) {}
}

final class CheckoutAction
{
    public function __construct(
        public readonly OrderService $orderService,
        public readonly UserService $userService,
        public readonly LoggerInterface $logger,
    ) {}
}

final class HealthCheck
{
    public function __construct(
        public readonly ClockInterface $clock,
    ) {}
}

final class OrderService
{
    public function __construct(
        public readonly OrderRepository $orderRepository,
        public readonly PaymentGatewayInterface $paymentGateway,
        public readonly InventoryGateway $inventoryGateway,
        public readonly TaxCalculator $taxCalculator,
        public readonly EventBusInterface $eventBus,
        public readonly UuidFactory $uuidFactory,
    ) {}
}

final class UserService
{
    public function __construct(
        public readonly UserRepositoryInterface $userRepository,
        public readonly SerializerInterface $serializer,
    ) {}
}

final class OrderRepository
{
    public function __construct(
        public readonly PdoConnection $connection,
        public readonly ClockInterface $clock,
    ) {}
}

final class UserRepository implements UserRepositoryInterface
{
    public function __construct(
        public readonly PdoConnection $connection,
        public readonly ClockInterface $clock,
    ) {}
}

final class PaymentGateway implements PaymentGatewayInterface
{
    public function __construct(
        public readonly HttpClient $httpClient,
        public readonly LoggerInterface $logger,
        public readonly SerializerInterface $serializer,
    ) {}
}

final class InventoryGateway
{
    public function __construct(
        public readonly HttpClient $httpClient,
        public readonly LoggerInterface $logger,
    ) {}
}

final class EventBus implements EventBusInterface
{
    public function __construct(
        public readonly LoggerInterface $logger,
        public readonly ClockInterface $clock,
    ) {}
}

final class HttpClient
{
    public function __construct(
        public readonly LoggerInterface $logger,
    ) {}
}

final class PdoConnection
{
    public function __construct() {}
}

final class TaxCalculator
{
    public function __construct() {}
}

final class UuidFactory
{
    public function __construct() {}
}

final class SystemClock implements ClockInterface
{}

final class NullLogger implements LoggerInterface
{}

final class JsonSerializer implements SerializerInterface
{}
