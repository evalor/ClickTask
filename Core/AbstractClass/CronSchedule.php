<?php

namespace eValor\cronTask\Core\AbstractClass;

use eValor\cronTask\Core\Cron;

/**
 * 计划任务调度器
 * Class CronSchedule
 * @author  : evalor <master@evalor.cn>
 * @package eValor\cronTask\Core\AbstractClass
 */
abstract class CronSchedule
{
    protected static $instance;

    protected $scheduler;

    function __construct()
    {
        $this->scheduler = new Cron;
    }

    static function instance()
    {
        if (!isset(self::$instance)) self::$instance = new static;
        return self::$instance;
    }

    function cron()
    {
        return $this->scheduler;
    }

    /**
     * 制定计划任务
     * @author : evalor <master@evalor.cn>
     */
    abstract function schedule();
}