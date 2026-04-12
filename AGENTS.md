# Repository Guidelines

## Project Structure & Module Organization

This is a small PHP library package for `takaram/nano-di`. Runtime code lives in `src/`, with the container implementation at `src/Container.php` and PSR-11 exception types under `src/Exception/`. Tests live in `tests/`; reusable test doubles and fixture classes belong in `tests/Fixture/`. The `benchmark/` directory is a separate Composer project for phpbench comparisons and should be changed independently from the library unless the benchmark scenario itself is part of the task.

## Build, Test, and Development Commands

Install dependencies with:

```bash
composer install
```

Use these Composer scripts from the repository root:

```bash
composer test        # Run PHPUnit using phpunit.xml.dist
composer phpstan     # Run PHPStan at max level over src/ and tests/
composer cs:check    # Check PER-CS formatting without changing files
composer cs:fix      # Apply php-cs-fixer formatting to src/ and tests/
```

For benchmarks, run commands inside `benchmark/`, for example `cd benchmark && composer bench:quick`.

## Coding Style & Naming Conventions

Use PHP 8.3+ features and keep `declare(strict_types=1);` at the top of PHP files. Follow PSR-4 namespaces: `Takaram\NanoDi\` maps to `src/`, and `Takaram\NanoDi\Tests\` maps to `tests/`. Formatting is managed by PHP-CS-Fixer with the `@PER-CS3x0` rule set, so prefer running `composer cs:fix` rather than hand-tuning whitespace. Name production classes by their role (`Container`, `NotFoundException`) and test fixture classes with the `Test...` prefix when they are not test cases.

## Testing Guidelines

Add or update PHPUnit tests for every behavioral change. Test case classes should extend `PHPUnit\Framework\TestCase`, live under `tests/`, and use descriptive method names such as `testGetThrowsContainerExceptionForUnknownId`. Put helper fixtures in `tests/Fixture/` rather than inside the main test class when they represent services, contracts, or dependency graphs. Run `composer test`, `composer phpstan`, and `composer cs:check` before submitting changes.

## Commit & Pull Request Guidelines

Recent commits use concise imperative subjects, for example `Cache resolved container instances` and `Add Composer test script`. Keep commit subjects short, specific, and focused on one change. Pull requests should describe the behavioral change, mention relevant issue links, and include the local verification commands run. Screenshots are normally unnecessary for this PHP library, but benchmark changes should include the command and summary result used for comparison.
