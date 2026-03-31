<?php

declare(strict_types=1);

namespace Takaram\NanoDi\Tests\Fixture;

final class TestOptionalScalarConsumer
{
    public function __construct(public string $name = 'default') {}
}
