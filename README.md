# nano-di

`takaram/nano-di` is a tiny PSR-11 dependency injection container for PHP.

It is intentionally small: no configuration DSL, no attributes, no service providers, and no compile step. In return, it gives you a lightweight container that can resolve ordinary constructor-injected objects with very little setup.

## Installation

```bash
composer require takaram/nano-di
```

## Requirements

- PHP 8.3 or later
- `psr/container` 2.0

## Quick Start

```php
<?php

declare(strict_types=1);

use App\Repository\UserRepository;
use Psr\Container\ContainerInterface;
use Takaram\NanoDi\Container;

final class UserService
{
    public function __construct(
        private UserRepository $users,
    ) {}
}

$container = new Container();

$service = $container->get(UserService::class);

var_dump($service instanceof UserService); // true
var_dump($container instanceof ContainerInterface); // true
```

When a constructor parameter has a concrete class type, or a mapped interface type, nano-di resolves that dependency recursively.

## Interface Mappings

Pass a simple map to bind an interface or abstract service id to a concrete class:

```php
<?php

declare(strict_types=1);

use App\Contract\Mailer;
use App\Service\SmtpMailer;
use App\Service\UserNotifier;
use Takaram\NanoDi\Container;

$container = new Container([
    Mailer::class => SmtpMailer::class,
]);

$notifier = $container->get(UserNotifier::class);
```

In this example, if `UserNotifier` depends on `Mailer`, nano-di will instantiate `SmtpMailer`.

## What It Does

- Implements `Psr\Container\ContainerInterface`
- Resolves concrete classes by class name
- Resolves constructor dependencies recursively
- Supports interface-to-class mappings through a plain array
- Reuses resolved objects for subsequent `get()` calls
- Uses default values for builtin constructor parameters when available
- Throws PSR-11 compatible exceptions for missing entries and container failures

## What It Leaves Out

nano-di is built for small applications, tests, CLIs, examples, and libraries that only need a minimal container. The current API deliberately leaves out larger container features such as:

- Factory callbacks
- Runtime parameters
- Attributes or annotations
- Autowiring rules beyond constructor type hints
- Service scopes
- Lazy proxies
- Compilation or caching layers

If your project needs those features, a full-featured DI container may be a better fit. If you only need constructor autowiring and a few interface mappings, nano-di keeps the moving parts low.

## Development

Install dependencies:

```bash
composer install
```

Run the test suite and checks:

```bash
composer test
composer phpstan
composer cs:check
```

## License

MIT
