<?php

// 应用公共函数库文件

/**
 * 记录日志
 * @param string $class
 * @param string $method
 * @param array $params
 * @return bool
 */
function dologs($class, $method, $params = [])
{
    $value = "behavior {$class} --{$method}";
    foreach ($params as $key => $val) {
        $value .= " --{$key} {$val}";
    }
    log_write($value);
    return true;
}
