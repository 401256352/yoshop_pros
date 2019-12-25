<?php

namespace app\common\model\dealer;

use app\common\model\BaseModel;

/**
 * 分销商申请模型
 * Class Apply
 * @package app\common\model\dealer
 */
class Apply extends BaseModel
{
    protected $name = 'dealer_apply';

    /**
     * 申请状态
     * @var array
     */
    public $applyStatus = [
        10 => '待审核',
        20 => '审核通过',
        30 => '驳回',
    ];

    /**
     * 获取器：申请时间
     * @param $value
     * @return false|string
     */
    public function getApplyTimeAttr($value)
    {
        return date('Y-m-d H:i:s', $value);
    }

    /**
     * 获取器：审核时间
     * @param $value
     * @return false|string
     */
    public function getAuditTimeAttr($value)
    {
        return $value > 0 ? date('Y-m-d H:i:s', $value) : 0;
    }

    /**
     * 关联推荐人表
     * @return \think\model\relation\BelongsTo
     */
    public function referee()
    {
        return $this->belongsTo('app\common\model\User', 'referee_id')
            ->field(['user_id', 'nickName']);
    }

    /**
     * 销商申请记录详情
     * @param $where
     * @return Apply|static
     * @throws \think\exception\DbException
     */
    public static function detail($where)
    {
        return self::get($where);
    }

    /**
     * 购买指定商品成为分销商
     * @param $userId
     * @param $goodsIds
     * @param $wxappId
     * @return bool
     * @throws \think\exception\DbException
     */
    public function becomeDealerUser($userId, $goodsIds, $wxappId)
    {
        // 验证是否设置
        $config = Setting::getItem('condition', $wxappId);
        if ($config['become__buy_goods'] != '1' || empty($config['become__buy_goods_ids'])) {
            return false;
        }
        // 判断商品是否在设置范围内
        $intersect = array_intersect($goodsIds, $config['become__buy_goods_ids']);
        if (empty($intersect)) {
            return false;
        }
        // 新增分销商用户
        User::add($userId, [
            'referee_id' => Referee::getRefereeUserId($userId, 1),
            'wxapp_id' => $wxappId,
        ]);
        return true;
    }

}