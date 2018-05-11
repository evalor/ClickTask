<?php

namespace eValor\cronTask\Core\AbstractClass;

/**
 * 定时任务执行者
 * Class CronRunner
 * @author  : evalor <master@evalor.cn>
 * @package eValor\cronTask\Core
 */
abstract class CronRunner
{

    protected $data;

    /**
     * CronRunner constructor.
     * @param $data
     */
    function __construct($data = null)
    {
        if (!is_null($data)) $this->setData($data);
    }

    /**
     * 设置Data数据
     * @param $data
     * @author : evalor <master@evalor.cn>
     */
    function setData($data)
    {
        $this->data = $data;
    }

    /**
     * 获取Data数据
     * @author : evalor <master@evalor.cn>
     * @return mixed
     */
    function getData()
    {
        return $this->data;
    }

    function time()
    {
        $time = date('Y-m-d H:i:s');
        return "[ $time ]";
    }

    /**
     * 任务执行
     * @author : evalor <master@evalor.cn>
     * @return mixed
     */
    abstract function run();

    /**
     * 任务异常处理
     * @param \Throwable $throwable
     * @author : evalor <master@evalor.cn>
     */
    abstract function onException(\Throwable $throwable);

    /**
     * 任务处理结束
     * @param $result
     * @author : evalor <master@evalor.cn>
     */
    abstract function onFinish($result);
}