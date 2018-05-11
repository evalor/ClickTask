# 自动签到助手

实现 `计划任务定时` `CURL集成封装` `签到任务模板类` `随机UA生成库` 为编写各大网站的自动签到提供基础工具库支持

## 环境要求

- PHP 7.1 以上版本
- Swoole 拓展版本不低于 1.9.23

## 为签到任务编写签到模板类

为了便于管理 签到任务模板统一存放在 `Runner` 目录下，必须继承 `eValor\click\Core\AbstractClass` 模板类，代码逻辑见下方

```php
<?php

namespace eValor\click\Runner;

use eValor\click\Core\AbstractClass\CronRunner;

/**
 * 签到处理
 * Class MaterialFire
 * @author  : evalor <master@evalor.cn>
 * @package eValor\click\Runner
 */
class MaterialFire extends CronRunner
{
    /**
     * 执行签到逻辑
     * @author : evalor <master@evalor.cn>
     * @return mixed
     * @throws \Exception
     */
    function run()
    {
       // 在这里编写网站登录 请求签到接口等逻辑
       // 如签到失败等情况 可抛出异常 由下面的 onException 方法处理
       // 框架在任务结束时会调用 onFinish 方法 可以收到这里返回的数据
       
       return true;
    }

    /**
     * 任务异常处理
     * @param \Throwable $throwable
     * @author : evalor <master@evalor.cn>
     */
    function onException(\Throwable $throwable)
    {
        // 任务发生异常时 会调用此处的方法 可以对异常进行统一处理和通知
    }

    /**
     * 任务处理结束
     * @param $result
     * @author : evalor <master@evalor.cn>
     */
    function onFinish($result)
    {
        // 任务成功执行逻辑 这里可以收到上方 run 方法返回的数据 做签到成功通知等
    }
}
```

## 注册签到计划任务

编写好签到任务后，在根目录的 `Go` 文件添加计划任务，会按计划任务的时间调度执行签到脚本

```php
<?php

namespace eValor\click;

use eValor\click\Account\MaterialFireAccount;
use eValor\click\Core\Cron;
use eValor\click\Core\Swoole;
use eValor\click\Runner\MaterialFire;

// 省略一部分管理脚本代码

// 实例化一个 Cron 管理类
$CronClass = new Cron;

// 此处实例化 Runner 类 可以在实例化的时候传入数据
$MaterialFire = new MaterialFire(new MaterialFireAccount);

// 以 Linux CronTab 的语法格式添加一个任务
$CronClass->addCronTask('MaterialFire', '*/1 * * * *', $MaterialFire); // 每分钟执行

// 启动框架并传入 Cron 管理类 开始执行签到任务调度
(new Swoole)->run($CronClass);
```

## 助手工具

系统封装了一些常用的助手工具类，方便编写登录签到以及控制台输出等操作，具体用法可以查看源码，或参考已有的一个 Runner 类

- `eValor\click\Core\ConsoleWrite` 终端控制工具
- `eValor\click\Core\Curl\UAGenerate` 生成UA标识符
- `eValor\click\Core\Curl\Request` 封装请求操作