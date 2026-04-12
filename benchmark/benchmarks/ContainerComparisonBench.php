<?php

declare(strict_types=1);

namespace Takaram\NanoDi\Benchmark\Benchmarks;

use PhpBench\Attributes\Iterations;
use PhpBench\Attributes\Revs;
use PhpBench\Attributes\Warmup;
use Takaram\NanoDi\Benchmark\ContainerFactory;

#[Iterations(10)]
#[Revs(1000)]
#[Warmup(2)]
final class ContainerComparisonBench
{
    public function benchNanoDi(): void
    {
        ContainerFactory::nanoDi()->get(ContainerFactory::targetClass());
    }

    public function benchPhpDi(): void
    {
        ContainerFactory::phpDi()->get(ContainerFactory::targetClass());
    }

    public function benchLeagueContainer(): void
    {
        ContainerFactory::leagueContainer()->get(ContainerFactory::targetClass());
    }
}
