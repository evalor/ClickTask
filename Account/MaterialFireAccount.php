<?php

namespace eValor\cronTask\Account;

/**
 * 素材火账号
 * Class MaterialFireAccount
 * @author  : evalor <master@evalor.cn>
 * @package eValor\cronTask\Account
 */
class MaterialFireAccount
{
    protected $username = '请填入自己的账号';
    protected $password = '请填入自己的密码';

    function __construct($username = null, $password = null)
    {
        if (!is_null($username)) $this->setUsername($username);
        if (!is_null($password)) $this->setPassword($password);
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     * @return MaterialFireAccount
     */
    public function setUsername(string $username): MaterialFireAccount
    {
        $this->username = $username;
        return $this;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return MaterialFireAccount
     */
    public function setPassword(string $password): MaterialFireAccount
    {
        $this->password = $password;
        return $this;
    }

}