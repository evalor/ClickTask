<?php

namespace eValor\click\Runner;

use eValor\click\Account\MaterialFireAccount;
use eValor\click\Core\AbstractClass\CronRunner;
use eValor\click\Core\ConsoleWrite;
use eValor\click\Core\Curl\Field;
use eValor\click\Core\Curl\Headers;
use eValor\click\Core\Curl\Request;
use eValor\click\Core\Curl\UAGenerate;

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
        /** @var MaterialFireAccount $account */
        $account = $this->data;

        $headers = (new Headers())
            ->setHeader('User-Agent', UAGenerate::mock(UAGenerate::SYS_OSX, false, UAGenerate::SYS_BIT_X64))
            ->setHeader('Connection', 'keep-alive')
            ->setHeader('Pragma', 'no-cache')
            ->setHeader('Cache-Control', 'no-cache');

        // 请求登录系统
        $request = new Request('http://www.sucaihuo.com/Login/check');
        $new_headers = $headers;
        $new_headers->setHeader('Referer', 'http://www.sucaihuo.com/login.html')
            ->setHeader('X-Requested-With', 'XMLHttpRequest');

        $response = $request
            ->setHeaders($new_headers)
            ->addPost(new Field('username', $account->getUsername()))
            ->addPost(new Field('pwd', $account->getPassword()))
            ->exec();

        if (!$enter = json_decode($response->getBody())) throw new \Exception('响应解析失败: ' . $response->getBody());
        if ($enter->error !== '') throw new \Exception('签到失败: ' . $enter->error);
        $cookies = $response->getCookies();

        // 请求换取签到令牌
        $token_request = new Request('http://www.sucaihuo.com/Member/sign');

        $new_headers = $headers;
        $new_headers->setHeader('Referer', 'http://www.sucaihuo.com/login.html');
        $response = $token_request->setCookies($cookies)->setHeaders($new_headers)->exec();
        $tokenCheck = preg_match('/data-key="(.*)?"/i', $response->getBody(), $token);
        if (!$tokenCheck) throw new \Exception('获取签到令牌失败');
        $enter->token = $token[1];

        // 请求操作签到接口
        $signDayRequest = new Request('http://www.sucaihuo.com/Member/signDay');
        $new_headers = $headers;
        $new_headers->setHeader('Referer', 'http://www.sucaihuo.com/Member/sign');
        $signDayResponse = $signDayRequest->setCookies($cookies)->addPost(new Field('key', $enter->token))->setHeaders($new_headers)->exec();
        $result = $signDayResponse->getBody();
        if ($result === 'key') throw new \Exception('签到令牌错误: ' . $enter->token);
        if ((int)$result <= 0) throw new \Exception('今天已经签到过了');
        $enter->score = (int)$result;
        return $enter;
    }

    /**
     * 任务异常处理
     * @param \Throwable $throwable
     * @author : evalor <master@evalor.cn>
     */
    function onException(\Throwable $throwable)
    {
        ConsoleWrite::color($this->time() . '[ MaterialFire ] ' . $throwable->getMessage() . PHP_EOL, ConsoleWrite::F_RED);
    }

    /**
     * 任务处理结束
     * @param $result
     * @author : evalor <master@evalor.cn>
     */
    function onFinish($result)
    {
        $username = $result->username;
        $userID = $result->userid;
        $token = strtoupper($result->token);
        $score = $result->score;
        ConsoleWrite::color($this->time() . "[ MaterialFire ] UID{$userID} => {$username} TOKEN {$token} 签到成功 获得{$score}积分\n", ConsoleWrite::F_GREEN);
    }
}