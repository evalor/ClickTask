<?php

namespace eValor\cronTask;

use eValor\cronTask\Account\MaterialFireAccount;
use eValor\cronTask\Core\AbstractClass\CronSchedule;
use eValor\cronTask\Runner\MaterialFire;

/**
 * 计划任务调度控制
 * Class Schedule
 * @author  : evalor <master@evalor.cn>
 * @package eValor\cronTask
 */
class Schedule extends CronSchedule
{
    /**
     * 制定计划任务
     * @author : evalor <master@evalor.cn>
     */
    function schedule()
    {
        $this->scheduler->addCronTask('MaterialFire', '15 4 * * *', new MaterialFire(new MaterialFireAccount));
    }
}