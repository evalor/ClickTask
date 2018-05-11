<?php

namespace eValor\click\Core\Curl;

/**
 * Curl Request
 * Class Request
 * @author  : evalor <master@evalor.cn>
 * @package eValor\click\Core\Curl
 */
class Request
{
    private $curlOPt = [
        CURLOPT_CONNECTTIMEOUT => 3,
        CURLOPT_TIMEOUT        => 10,
        CURLOPT_AUTOREFERER    => true,
        CURLOPT_USERAGENT      => "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.12; rv:61.0) Gecko/20100101 Firefox/61.0",
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_HEADER         => true,
    ];
    private $fields  = [];
    private $cookies = [];

    /**
     * Request constructor.
     * @param null|string $url 需要请求的连接
     */
    function __construct($url = null)
    {
        if ($url !== null) {
            $this->setUrl($url);
        }
    }

    /**
     * 设置目标请求连接
     * @param string $url
     * @author : evalor <master@evalor.cn>
     * @return $this
     */
    public function setUrl($url)
    {
        $this->curlOPt[CURLOPT_URL] = $url;
        return $this;
    }

    /**
     * 添加一个 Cookie
     * @param Cookie $cookie
     * @author : evalor <master@evalor.cn>
     * @return Request
     */
    public function addCookie($cookie)
    {
        $this->cookies[$cookie->getName()] = $cookie->getValue();
        return $this;
    }

    /**
     * 覆盖所有Cookie
     * @param $cookies
     * @author : evalor <master@evalor.cn>
     * @return Request
     */
    public function setCookies($cookies)
    {
        foreach ($cookies as $cookie) {
            $this->addCookie($cookie);
        }
        return $this;
    }

    /**
     * 批量设置 Headers
     * @param Headers $headers
     * @author : evalor <master@evalor.cn>
     * @return Request
     */
    public function setHeaders(Headers $headers)
    {
        $this->curlOPt[CURLOPT_HTTPHEADER] = $headers->__toArray();
        return $this;
    }

    /**
     * 设置 POST 参数
     * @param Field $field
     * @param bool  $isFile
     * @author : evalor <master@evalor.cn>
     * @return Request
     */
    public function addPost(Field $field, $isFile = false)
    {
        $this->fields['post'][$field->getName()] = $isFile ? new \CURLFile($field->getVal()) : $field->getVal();
        return $this;
    }

    /**
     * 设置 GET 参数
     * @param Field $field
     * @author : evalor <master@evalor.cn>
     * @return $this
     */
    public function addGet(Field $field)
    {
        $this->fields['get'][$field->getName()] = $field->getVal();
        return $this;
    }

    /**
     * 设置 CURL 配置
     * @param array $opt
     * @param bool  $isMerge
     * @author : evalor <master@evalor.cn>
     * @return Request
     */
    public function setUserOpt(array $opt, $isMerge = true)
    {
        if ($isMerge) {
            $this->curlOPt = $opt + $this->curlOPt;
        } else {
            $this->curlOPt = $opt;
        }
        return $this;
    }

    /**
     * 执行请求
     * @author : evalor <master@evalor.cn>
     * @return Response
     */
    public function exec()
    {
        $curl = curl_init();
        curl_setopt_array($curl, $this->getOpt());
        $result = curl_exec($curl);
        return new Response($result, $curl);
    }

    public function getOpt(): array
    {
        $opt = $this->curlOPt;
        if (!empty($this->cookies)) {
            $str = '';
            foreach ($this->cookies as $name => $value) {
                $str .= "{$name}={$value};";
            }
            $opt[CURLOPT_COOKIE] = $str;
        }
        if (isset($this->fields['get'])) {
            $query = http_build_query($this->fields['get']);
            $opt[CURLOPT_URL] = rtrim($opt[CURLOPT_URL], '?') . '?' . $query;
        }
        if (isset($this->fields['post'])) {
            //若用户已经设置了POST  则opt中的优先级最高
            if (!isset($opt[CURLOPT_POSTFIELDS])) {
                $opt[CURLOPT_POST] = true;
                $opt[CURLOPT_POSTFIELDS] = $this->fields['post'];
            }
        }
        return $opt;
    }
}