<?php

namespace app\common\model\bargain;

use think\Cache;
use app\common\model\BaseModel;

/**
 * 砍价活动设置模型
 * Class Setting
 * @package app\common\model\bargain
 */
class Setting extends BaseModel
{
    protected $name = 'bargain_setting';
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
        $cacheKey = "bargain_setting_{$wxapp_id}";
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
                    // 砍价规则
                    'bargain_rules' => "活动期间，用户可以在砍价活动页选择活动商品发起砍价，可通过微信分享砍价商品活动页面给好友，并通过好友助力砍价，将商品砍至一定金额。\n\n" .
                        "每次砍价金额随机，可砍出最高商品日常价内的随机金额，参与好友越多越容易成功。\n\n" .
                        "以最终砍价后的优惠价格购买该商品，且用户须在活动时间结束之前进行支付购买，否则砍价商品价格将过期失效。\n\n" .
                        "商品库存有限，以前台展示的库存数量为准，先到先得。",
                    // 模板消息
                    'template_msg' => []
                ]
            ]
        ];
    }

}