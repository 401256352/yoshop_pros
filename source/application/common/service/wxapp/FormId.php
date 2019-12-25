<?php

namespace app\common\service\wxapp;

use app\common\service\Basics;
use app\common\model\wxapp\Formid as FormidModel;

/**
 * 微信小程序formid类
 * Class FormId
 * @package app\common\service\wxapp
 */
class FormId extends Basics
{
    /**
     * 获取一个可用的formid
     * @param $userId
     * @return bool|mixed
     */
    public static function getAvailableFormId($userId)
    {
        if (!$formId = FormidModel::getAvailable($userId)) {
            return false;
        }
        return $formId;
    }

    /**
     * 将formid标记为已使用
     * @param $id
     * @return FormidModel
     */
    public static function setIsUsed($id)
    {
        return FormidModel::setIsUsed($id);
    }

}