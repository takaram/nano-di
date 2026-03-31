<?php
declare(strict_types=1);

namespace Takaram\NanoDi\Tests\Fixture;

final class TestCircularDependencyB
{
    public function __construct(public TestCircularDependencyA $dependency)
    {
    }
}
