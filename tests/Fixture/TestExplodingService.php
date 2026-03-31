<?php
declare(strict_types=1);

namespace Takaram\NanoDi\Tests\Fixture;

use RuntimeException;

final class TestExplodingService
{
    public function __construct()
    {
        throw new RuntimeException('boom');
    }
}
