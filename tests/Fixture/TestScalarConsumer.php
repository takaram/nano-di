<?php

declare(strict_types=1);

namespace Takaram\NanoDi\Tests\Fixture;

final class TestScalarConsumer
{
    public function __construct(public string $name) {}
}
