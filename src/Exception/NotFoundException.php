<?php
declare(strict_types=1);

namespace Takaram\NanoDi\Exception;

use Psr\Container\NotFoundExceptionInterface;

class NotFoundException extends ContainerException implements NotFoundExceptionInterface
{
}
