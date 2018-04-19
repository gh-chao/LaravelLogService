<?php

namespace Lilocon\LaravelLogService\Logging;


use Lilocon\LaravelLogService\Monolog\Handler;

class Factory
{
    /**
     * @param  \Illuminate\Log\Logger|\Monolog\Logger $logger
     *
     * @return void
     */
    public function __invoke($logger)
    {
        $aliyunLogServiceHandler = new Handler();

        $bufferHandler = new \Monolog\Handler\BufferHandler($aliyunLogServiceHandler, 100);

        $logger->pushHandler($bufferHandler);
    }
}
