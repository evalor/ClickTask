<?php

namespace eValor\cronTask\Core\Curl;

/**
 * Curl Response
 * Class Response
 * @author  : evalor <master@evalor.cn>
 * @package eValor\cronTask\Core\Curl
 */
class Response
{
    private $cookies    = [];
    private $body       = '';
    private $error;
    private $errorNo    = null;
    private $curlInfo   = [];
    private $headerLine = '';

    /**
     * Response constructor.
     * @param $rawResponse
     * @param $curlResource
     */
    function __construct($rawResponse, $curlResource)
    {
        $this->curlInfo = curl_getinfo($curlResource);
        $this->error = curl_error($curlResource);
        $this->errorNo = curl_errno($curlResource);
        $this->headerLine = substr($rawResponse, 0, $this->curlInfo['header_size']);
        $this->body = substr($rawResponse, $this->curlInfo['header_size']);

        //处理头部中的cookie
        preg_match_all("/Set-Cookie:(.*)\n/U", $this->headerLine, $ret);
        if (!empty($ret[0])) {
            foreach ($ret[0] as $item) {
                preg_match('/(Cookie: )(.*?)(\r\n)/', $item, $ret);
                $ret = explode('=', trim($ret[2], ';'));
                $cookie = new Cookie();
                $cookie->setName($ret[0]);
                $cookie->setValue($ret[1]);
                $this->cookies[$ret[0]] = $cookie;
            }
        }
        curl_close($curlResource);
    }

    /**
     * 获取所有 Cookie
     * @author : evalor <master@evalor.cn>
     * @return array
     */
    public function getCookies()
    {
        return $this->cookies;
    }

    /**
     * 获取单个 Cookie
     * @param $name
     * @author : evalor <master@evalor.cn>
     * @return Cookie|null
     */
    public function getCookie($name)
    {
        if (isset($this->cookies[$name])) {
            return $this->cookies[$name];
        } else {
            return null;
        }
    }

    /**
     * 获取请求 Body
     * @author : evalor <master@evalor.cn>
     * @return bool|string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * 获取请求错误文本
     * @author : evalor <master@evalor.cn>
     * @return string
     */
    public function getError(): string
    {
        return $this->error;
    }

    /**
     * 获取请求错误代码
     * @author : evalor <master@evalor.cn>
     * @return int|null
     */
    public function getErrorNo()
    {
        return $this->errorNo;
    }

    /**
     * 获取 CurlInfo
     * @author : evalor <master@evalor.cn>
     * @return array|mixed
     */
    public function getCurlInfo()
    {
        return $this->curlInfo;
    }

    /**
     * 获取原生响应头
     * @author : evalor <master@evalor.cn>
     * @return bool|string
     */
    public function getHeaderLine()
    {
        return $this->headerLine;
    }

    public function __toString()
    {
        return $this->getBody();
    }
}