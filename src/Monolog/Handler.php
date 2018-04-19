<?php

namespace Lilocon\LaravelLogService\Monolog;


use Lilocon\LaravelLogService\Jobs\PutLogs;
use Monolog\Handler\AbstractHandler;

class Handler extends AbstractHandler
{

    public function handleBatch(array $records)
    {
        $items = [];

        foreach ($records as $record) {
            array_push($items, $this->getFormatter()->format($record));
        }

        if ($items) {
            dispatch(new PutLogs($items));
        }
    }

    public function handle(array $record)
    {
        $format = $this->getFormatter()->format($record);
        dispatch(new PutLogs([$format]));
    }

    protected function getDefaultFormatter()
    {
        return new Formatter();
    }
}
