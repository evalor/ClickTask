<?php

namespace eValor\cronTask\Core\Utils;

/**
 * i18n
 * Class Locale
 * @author  : evalor <master@evalor.cn>
 * @package eValor\cronTask\Core\Utils
 */
class Locale
{
    protected static $localeDir = __DIR__ . DIRECTORY_SEPARATOR . 'Locale' . DIRECTORY_SEPARATOR;
    protected static $i18n;

    /**
     * 为当前执行环境设置 I18N 常量以及读取配置
     * @author : evalor <master@evalor.cn>
     */
    static function set()
    {
        $locale = setlocale(LC_CTYPE, null);
        if (!$locale) define('I18N', 'en_US');
        $localization = self::$localeDir . strstr($locale, '.', true) . '.php';
        if (!file_exists($localization)) $localization = self::$localeDir . 'en_US.php';
        self::$i18n = require_once $localization;
    }

    /**
     * 获取本地化翻译
     * @param string $name 本地化字符串
     * @param array  $vars 本地化变量替换
     * @author : evalor <master@evalor.cn>
     * @return mixed|string
     */
    static function translate($name = '', $vars = [])
    {
        if ($name == '') return self::$i18n;
        $value = isset(self::$i18n[$name]) ? self::$i18n[$name] : $name;

        if (key($vars) === 0) {
            array_unshift($vars, $value);
            $value = call_user_func_array('sprintf', $vars);
        } else {
            $replace = array_keys($vars);
            foreach ($replace as &$v) {
                $v = "{:{$v}}";
            }
            $value = str_replace($replace, $vars, $value);
        }
        return $value;
    }
}

