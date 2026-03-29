<?php
declare(strict_types=1);

namespace Takaram\NanoDi\Tests\Fixture;

final class TestConsumer
{
    public function __construct(public TestServiceContract $service)
    {
    }
}
