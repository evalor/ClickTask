<?php

namespace eValor\cronTask\Core;

use eValor\cronTask\Core\Utils\Locale;

/**
 * 当发生一些事件的时候快捷向控制台输出内容
 * Class ConsoleEvent
 * @author  : evalor <master@evalor.cn>
 * @package eValor\Core
 */
class ConsoleEvent
{
    static function time($eventName = '')
    {
        $time = date('Y-m-d H:i:s');
        if ($eventName !== '') $eventName = ' ' . $eventName;
        return "[{$time}$eventName] ";
    }

    /**
     * 向调度器注册任务
     * @param $time
     * @param $task
     * @author : evalor <master@evalor.cn>
     */
    static function REGISTER($time, $task)
    {
        ConsoleWrite::color(
            ConsoleEvent::time('RESERVE') . Locale::translate(
                '{:task} will be executed at {:time}',
                ['task' => $task, 'time' => $time]
            ) . PHP_EOL,
            ConsoleWrite::F_BLUE
        );
    }

    /**
     * 延时投递任务
     * @param $sec
     * @param $task
     * @author : evalor <master@evalor.cn>
     */
    static function DELIVER($sec, $task)
    {
        ConsoleWrite::color(
            ConsoleEvent::time('DELIVER') . Locale::translate(
                'Planned task {:task} will be executed after {:sec} seconds',
                ['task' => $task, 'sec' => $sec]
            ) . '...' . PHP_EOL,
            ConsoleWrite::F_BLUE
        );
    }

    /**
     * 任务处理失败
     * @param $name
     * @param $reason
     * @author : evalor <master@evalor.cn>
     */
    static function TASKERR($name, $reason)
    {
        ConsoleWrite::color(
            ConsoleEvent::time($name) . Locale::translate(
                'Planned task failed because {:reason}',
                ['reason' => $reason]
            ) . PHP_EOL,
            ConsoleWrite::F_RED
        );
    }
}