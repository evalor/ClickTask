<?php

namespace eValor\cronTask\Core;

/**
 * 服务管理器
 * Class Swoole
 * @author  : evalor <master@evalor.cn>
 * @package eValor\cronTask\Core
 */
class Swoole
{
    /** @var Cron $cron */
    protected $cron;

    /**
     * 启动主服务
     * @author : evalor <master@evalor.cn>
     * @param Cron $cron
     */
    public function run(Cron $cron)
    {
        $this->cron = $cron;
        $options = require __DIR__ . DIRECTORY_SEPARATOR . 'SwooleOptions.php';
        $server = new \swoole_websocket_server('0.0.0.0', 9501);
        $server->on('request', [$this, 'onRequest']);
        $server->on('message', [$this, 'onMessage']);
        $server->on('workerstart', [$this, 'onWorkerStart']);
        $server->set($options['MAIN_SERVER']['SETTING']);
        $server->start();
    }

    /**
     * 收到请求时
     * @param \swoole_http_request  $request
     * @param \swoole_http_response $response
     * @author : evalor <master@evalor.cn>
     */
    public function onRequest(\swoole_http_request $request, \swoole_http_response $response)
    {
        $result = [];
        exec('tail -n 30 ' . __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Runtime' . DIRECTORY_SEPARATOR . 'cron.log', $result);
        $response->header('Content-Type', 'text/html;charset=utf-8');
        foreach ($result as $item) {
            $response->write($item . '</br>');
        }
        $response->end('----------------EOF----------------');
    }

    /**
     * 收到消息时
     * @param \swoole_server          $server
     * @param \swoole_websocket_frame $frame
     * @author : evalor <master@evalor.cn>
     */
    public function onMessage(\swoole_server $server, \swoole_websocket_frame $frame)
    {

    }

    /**
     * Worker启动时
     * @param \swoole_server $server
     * @param int            $worker_id
     * @author : evalor <master@evalor.cn>
     */
    public function onWorkerStart(\swoole_server $server, $worker_id)
    {
        if ($worker_id === 0) {
            $this->cron->run();
        }
    }
}