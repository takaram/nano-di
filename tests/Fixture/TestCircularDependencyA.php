<?php

declare(strict_types=1);

namespace Takaram\NanoDi\Tests\Fixture;

final class TestCircularDependencyA
{
    public function __construct(public TestCircularDependencyB $dependency) {}
}
