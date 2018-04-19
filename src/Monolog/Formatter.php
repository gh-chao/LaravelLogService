<?php

namespace Lilocon\LaravelLogService\Monolog;

class Formatter extends \Monolog\Formatter\JsonFormatter
{
    /**
     * @param array $record
     *
     * @return array|mixed|null|string|string[]
     */
    public function format(array $record)
    {
        if (defined('REQUEST_ID')) {
            $record['context']['req_id'] = REQUEST_ID;
        }

        return [
            'datetime' => $record['datetime']->getTimestamp(),
            'message'  => $record['message'],
            'channel'  => $record['channel'],
            'level'    => $record['level_name'],
            'context'  => $this->normalize($record['context']),
        ];
    }

}
