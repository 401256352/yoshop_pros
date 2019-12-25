<?php

namespace app\common\service;

class Basics
{
    // 错误信息
    protected $error;

    // 当前小程序id
    protected $wxappId;

    /**
     * 获取错误信息
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * 是否存在错误
     * @return bool
     */
    public function hasError()
    {
        return !empty($this->error);
    }

}