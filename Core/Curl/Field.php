<?php

namespace eValor\click\Core\Curl;

/**
 * HTTP Field Class
 * Class Field
 * @author  : evalor <master@evalor.cn>
 * @package eValor\click\Core\Curl
 */
class Field
{
    private $name;
    private $val;

    function __construct($key = null, $val = null)
    {
        $this->name = $key;
        $this->val = $val;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return Field
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getVal()
    {
        return $this->val;
    }

    /**
     * @param mixed $val
     * @return Field
     */
    public function setVal($val)
    {
        $this->val = $val;
        return $this;
    }

    public function toArray()
    {
        return array(
            $this->name => $this->val
        );
    }
}