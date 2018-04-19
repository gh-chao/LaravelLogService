<?php

namespace Lilocon\LaravelLogService\Jobs;

use Aliyun\SLS\Client;
use Aliyun\SLS\Models\LogItem;
use Aliyun\SLS\Requests\PutLogsRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PutLogs implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * @var array
     */
    private $logs;

    /**
     * Create a new job instance.
     *
     * @param array $logs
     */
    public function __construct(array $logs)
    {
        $this->logs = $logs;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $items = [];

        foreach ($this->logs as $log) {
            $item = new LogItem();
            $item->setTime($log['datetime']);
            $item->setData([
                'message' => $log['message'],
                'channel' => $log['channel'],
                'level'   => $log['level'],
                'context' => json_encode($log['context']),
            ]);
            array_push($items, $item);
        }

        $putLogsRequest = new PutLogsRequest(config('log-service.project'), config('log-service.logstore'));
        $putLogsRequest->setLogItems($items);

        app(Client::class)->putLogs($putLogsRequest);
    }
}
