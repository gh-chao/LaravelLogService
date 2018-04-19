<?php

namespace Lilocon\LaravelLogService\Logging;

use Lilocon\LaravelLogService\Monolog\Handler;
use Monolog\Logger;

class CreateLogger
{
    public function __invoke(array $config)
    {
        return new Logger('LogService', [new Handler()]);
    }
}
