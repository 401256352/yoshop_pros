<?php

namespace app\api\model;

use app\common\library\helper;
use app\common\model\Goods as GoodsModel;
use app\api\service\Goods as GoodsService;

/**
 * 商品模型
 * Class Goods
 * @package app\api\model
 */
class Goods extends GoodsModel
{
    /**
     * 隐藏字段
     * @var array
     */
    protected $hidden = [
        'spec_rel',
        'delivery',
        'sales_initial',
        'sales_actual',
        'is_delete',
        'wxapp_id',
        'create_time',
        'update_time'
    ];

    /**
     * 商品详情：HTML实体转换回普通字符
     * @param $value
     * @return string
     */
    public function getContentAttr($value)
    {
        return htmlspecialchars_decode($value);
    }

    /**
     * 获取商品列表
     * @param $param
     * @param bool $userInfo
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function getList($param, $userInfo = false)
    {
        // 获取商品列表
        $data = parent::getList($param);
        // 隐藏api属性
        !$data->isEmpty() && $data->hidden(['category', 'content', 'image', 'sku']);
        // 整理列表数据并返回
        return $this->setGoodsListDataFromApi($data, true, ['userInfo' => $userInfo]);
    }

    /**
     * 商品详情
     * @param $goodsId
     * @return GoodsModel
     */
    public static function detail($goodsId)
    {
        // 商品详情
        $detail = parent::detail($goodsId);
        // 多规格商品sku信息
        $detail['goods_multi_spec'] = GoodsService::getSpecData($detail);
        return $detail;
    }

    /**
     * 获取商品详情页面
     * @param int $goodsId 商品id
     * @param array|bool $userInfo 用户信息
     * @return array|bool|false|mixed|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getDetails($goodsId, $userInfo = false)
    {
        // 获取商品详情
        $detail = $this->with([
            'category',
            'image' => ['file'],
            'sku' => ['image'],
            'spec_rel' => ['spec'],
            'delivery' => ['rule'],
            'commentData' => function ($query) {
                $query->with('user')->where(['is_delete' => 0, 'status' => 1])->limit(2);
            }
        ])->withCount(['commentData' => function ($query) {
            $query->where(['is_delete' => 0, 'status' => 1]);
        }])
            ->where('goods_id', '=', $goodsId)
            ->find();
        // 判断商品的状态
        if (empty($detail) || $detail['is_delete'] || $detail['goods_status']['value'] != 10) {
            $this->error = '很抱歉，商品信息不存在或已下架';
            return false;
        }
        // 设置商品展示的数据
        $detail = $this->setGoodsListDataFromApi($detail, false, ['userInfo' => $userInfo]);
        // 多规格商品sku信息
        $detail['goods_multi_spec'] = GoodsService::getSpecData($detail);
        return $detail;
    }

    /**
     * 根据商品id集获取商品列表
     * @param $goodsIds
     * @param bool $userInfo
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getListByIdsFromApi($goodsIds, $userInfo = false)
    {
        // 获取商品列表
        $data = parent::getListByIds($goodsIds, 10);
        // 整理列表数据并返回
        return $this->setGoodsListDataFromApi($data, true, ['userInfo' => $userInfo]);
    }


    /**
     * 设置商品展示的数据 api模块
     * @param $data
     * @param bool $isMultiple
     * @param array $param
     * @return mixed
     */
    private function setGoodsListDataFromApi(&$data, $isMultiple, $param)
    {
        return parent::setGoodsListData($data, $isMultiple, function ($goods) use ($param) {
            // 计算并设置商品会员价
            $this->setGoodsGradeMoney($param['userInfo'], $goods);
        });
    }

    /**
     * 设置商品的会员价
     * @param $user
     * @param $goods
     */
    private function setGoodsGradeMoney($user, &$goods)
    {
        // 会员等级状态
        $gradeStatus = (!empty($user) && $user['grade_id'] > 0 && !empty($user['grade']))
            && (!$user['grade']['is_delete'] && $user['grade']['status']);
        // 判断商品是否参与会员折扣
        if (!$gradeStatus || !$goods['is_enable_grade']) {
            $goods['is_user_grade'] = false;
            return;
        }
        // 商品单独设置了会员折扣
        if ($goods['is_alone_grade'] && isset($goods['alone_grade_equity'][$user['grade_id']])) {
            // 折扣比例
            $discountRatio = helper::bcdiv($goods['alone_grade_equity'][$user['grade_id']], 10);
        } else {
            // 折扣比例
            $discountRatio = helper::bcdiv($user['grade']['equity']['discount'], 10);
        }
        if ($discountRatio > 0) {
            // 标记参与会员折扣
            $goods['is_user_grade'] = true;
            // 会员折扣价
            foreach ($goods['sku'] as &$skuItem) {
                $skuItem['goods_price'] = helper::number2(helper::bcmul($skuItem['goods_price'], $discountRatio), true);
            }
        }
    }

}
