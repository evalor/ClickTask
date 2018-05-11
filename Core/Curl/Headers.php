<?php

namespace eValor\click\Core\Curl;

/**
 * 创建HTTP请求头
 * Class Headers
 * @author  : evalor <master@evalor.cn>
 * @package eValor\click\Core\Curl
 */
class Headers
{
    protected $headers = [];

    function __construct(array $headers = null)
    {
        if (!is_null($headers)) $this->setHeaders($headers);
    }

    /**
     * 获取所有 header
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * 设置所有 header
     * @param array $headers
     */
    public function setHeaders(array $headers): void
    {
        $this->headers = $headers;
    }

    /**
     * 获取一个 header
     * @param string $name
     * @author : evalor <master@evalor.cn>
     * @return mixed
     */
    public function getHeader($name)
    {
        return isset($this->headers[$name]) ? $this->headers[$name] : '';
    }

    /**
     * 设置一个 header
     * @param string $name
     * @param string $value
     * @author : evalor <master@evalor.cn>
     * @return Headers
     */
    public function setHeader($name, $value)
    {
        $this->headers[$name] = $value;
        return $this;
    }

    /**
     * 转为数组
     * @author : evalor <master@evalor.cn>
     * @return array
     */
    public function __toArray()
    {
        $format = [];
        foreach ($this->headers as $name => $value) {
            $format[] = "{$name}: {$value}";
        }
        return $format;
    }
}