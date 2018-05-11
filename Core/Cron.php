<?php

namespace eValor\click\Core;

use Cron\CronExpression;
use eValor\click\Core\AbstractClass\CronRunner;

/**
 * 计划任务管理类
 * Class Cron
 * @author  : evalor <master@evalor.cn>
 * @package eValor\click\Core
 */
class Cron
{
    protected $tasks = [];

    /**
     * 添加一个待执行的任务
     * @param string $taskName
     * @param string $rule
     * @param string $task
     * @author : evalor <master@evalor.cn>
     * @return Cron
     */
    function addCronTask($taskName, $rule, $task)
    {
        try {
            $next = CronExpression::factory($rule)->getNextRunDate()->format('Y-m-d H:i:s');

            if (is_string($task) && !class_exists($task)) {
                throw new \Exception('the task ' . $taskName . ' class ' . $task . 'does not exist');
            }

            if (is_string($task) && !((new $task) instanceof CronRunner)) {
                throw new \Exception('the task ' . $taskName . ' must be instanceof CronRunner class');
            }

            if (is_object($task) && !($task instanceof CronRunner)) {
                throw new \Exception('the task ' . $taskName . ' must be instanceof CronRunner class');
            }

            ConsoleWrite::color("[ REGISTER ] 将于 {$next} 执行签到脚本 {$taskName}\n", ConsoleWrite::F_BLUE);
            $this->tasks[$taskName] = [
                'name' => $taskName,
                'rule' => $rule,
                'task' => $task,
            ];
        } catch (\Throwable $throwable) {
            ConsoleWrite::color('[ REGISTER ] failure because ' . $throwable->getMessage() . PHP_EOL, ConsoleWrite::F_RED);
        }

        return $this;
    }

    /**
     * 启动计划任务监视进程
     * @author : evalor <master@evalor.cn>
     */
    function run()
    {
        $taskProcess = function () {
            $doCron = function () {
                $allToDo = [];
                $current = time();

                // 若相差小于30秒则投递执行
                foreach ($this->tasks as $task) {
                    $rule = $task['rule'];
                    $time = CronExpression::factory($rule)->getNextRunDate()->getTimestamp();
                    // 即将执行该任务
                    if ($time > $current && ($time - $current <= 30)) {
                        $task['sec'] = $time - $current;
                        $allToDo[] = $task;
                    }
                }

                if (!empty($allToDo)) {
                    // 延迟到目标时间执行
                    foreach ($allToDo as $task) {
                        ConsoleWrite::color('[ ' . date('Y-m-d H:i:s') . ' ]' . '[ TASK ] 正在延时投递签到任务 ' . $task['name'] . ' 将于 ' . $task['sec'] . ' 秒后被执行..' . PHP_EOL, ConsoleWrite::F_BLUE);
                        swoole_timer_after($task['sec'] * 1000, function () use ($task) {
                            (new \swoole_process(function () use ($task) {
                                $taskObject = $task['task'];
                                /** @var CronRunner $taskObject */
                                if (is_string($taskObject)) $taskObject = new $taskObject;
                                try {
                                    $result = $taskObject->run();
                                    $taskObject->onFinish($result);
                                } catch (\Throwable $throwable) {
                                    $taskObject->onException($throwable);
                                }
                            }, false, false))->start();
                        });
                    }
                }
            };
            $doCron();
            swoole_timer_tick(30000, $doCron);
        };

        // 计划任务管理进程启动
        (new \swoole_process($taskProcess, false, false))->start();
    }
}