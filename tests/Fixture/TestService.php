<?php
declare(strict_types=1);

namespace Takaram\NanoDi\Tests\Fixture;

final class TestService implements TestServiceContract
{
    public function __construct(public TestDependency $dependency)
    {
    }
}
