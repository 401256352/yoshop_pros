<?php

namespace app\common\model\sharp;

use think\Cache;
use app\common\model\BaseModel;

/**
 * 整点秒杀设置模型
 * Class Setting
 * @package app\common\model\sharp
 */
class Setting extends BaseModel
{
    protected $name = 'sharp_setting';
    protected $createTime = false;

    /**
     * 获取器: 转义数组格式
     * @param $value
     * @return mixed
     */
    public function getValuesAttr($value)
    {
        return json_decode($value, true);
    }

    /**
     * 修改器: 转义成json格式
     * @param $value
     * @return string
     */
    public function setValuesAttr($value)
    {
        return json_encode($value);
    }

    /**
     * 获取指定项设置
     * @param $key
     * @param $wxapp_id
     * @return array
     */
    public static function getItem($key, $wxapp_id = null)
    {
        $data = static::getAll($wxapp_id);
        return isset($data[$key]) ? $data[$key]['values'] : [];
    }

    /**
     * 获取全部设置
     * @param null $wxapp_id
     * @return array|mixed
     */
    public static function getAll($wxapp_id = null)
    {
        $self = new static;
        is_null($wxapp_id) && $wxapp_id = $self::$wxapp_id;
        $cacheKey = "sharp_setting_{$wxapp_id}";
        if (!$data = Cache::get($cacheKey)) {
            $data = array_column(collection($self::all())->toArray(), null, 'key');
            Cache::tag('cache')->set($cacheKey, $data);
        }
        return array_merge_multiple($self->defaultData(), $data);
    }

    /**
     * 获取设置项信息
     * @param $key
     * @return null|static
     * @throws \think\exception\DbException
     */
    public static function detail($key)
    {
        return static::get(compact('key'));
    }

    /**
     * 默认配置
     * @return array
     */
    public function defaultData()
    {
        return [
            'basic' => [
                'key' => 'basic',
                'describe' => '基础设置',
                'values' => [
                    // 是否开启分销
                    'is_dealer' => '0',
                    'order' => [
                        // 秒杀订单未支付n分钟后自动关闭
                        'order_close' => '10',
                    ]
                ]
            ]
        ];
    }

}