<?php

namespace Lilocon\LaravelLogService\Controller;

use Aliyun\SLS\Models\QueriedLog;
use Aliyun\SLS\Requests\GetLogsRequest;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Routing\Controller;

class ViewerController extends Controller
{

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Aliyun\SLS\Exception
     */
    public function index(Request $request)
    {
        if ($request->isMethod('POST')) {
            /** @var \Aliyun\SLS\Client $client */
            $client = app('log-service.client');

            $page = (int)$request->get('page', 1);

            $project  = config('log-service.project');
            $logstore = config('log-service.logstore');

            $from = strtotime($request->get('begin'));
            $to   = strtotime($request->get('end'));

            $query  = $request->get('query', '*');
            $line   = 10;
            $offset = $line * ($page - 1);

            // 查询总记录数
            $getLogsRequest = new GetLogsRequest($project, $logstore, $from, $to, null, $query . '|select count(*)',
                $line,
                $offset);

            /** @var \Aliyun\SLS\Responses\GetLogsResponse $getLogsResponse */
            $getLogsResponse = $client->getLogs($getLogsRequest);

            $total = array_first(array_first($getLogsResponse->getLogs())->getContents());


            // 查询记录
            $getLogsRequest  = new GetLogsRequest($project, $logstore, $from, $to, null, $query, $line, $offset, true);
            $getLogsResponse = $client->getLogs($getLogsRequest);

            $paginator = new LengthAwarePaginator($getLogsResponse->getLogs(), $total, $line, $page, [
                'path' => Paginator::resolveCurrentPath(),
            ]);


            $data = array_map(function (QueriedLog $log) {

                return [
                    'time'       => date('Y-m-d H:i:s', $log->getTime()),
                    'channel'    => array_get($log->getContents(), 'channel'),
                    '__source__' => $log->getSource(),
                    'level'      => array_get($log->getContents(), 'level'),
                    'message'    => array_get($log->getContents(), 'message'),
                    'context'    => array_get($log->getContents(), 'context'),
                ];

            }, $paginator->items());


            return [
                'data' => $data,
                'meta' => [
                    'pagination' => [
                        'count'        => (int)$paginator->count(),
                        'current_page' => (int)$paginator->currentPage(),
                        'pre_page'     => (int)$paginator->perPage(),
                        'total'        => (int)$paginator->total(),
                        'total_pages'  => (int)ceil($paginator->total() / $paginator->perPage()),
                    ]
                ]
            ];
        }

        return view('log-service::viewer');
    }
}
