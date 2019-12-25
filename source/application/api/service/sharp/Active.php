<?php

namespace app\api\service\sharp;

use app\api\service\Basics;
use app\api\model\Goods as GoodsModel;
use app\api\model\sharp\Goods as SharpGoodsModel;
use app\api\model\sharp\Active as ActiveModel;
use app\api\model\sharp\ActiveTime as ActiveTimeModel;
use app\api\model\sharp\ActiveGoods as ActiveGoodsModel;
use app\common\enum\sharp\GoodsStatus as GoodsStatusEnum;
use app\common\enum\sharp\ActiveStatus as ActiveStatusEnum;
use app\common\library\helper;

/**
 * 秒杀活动服务类
 * Class Active
 * @package app\api\service\sharp
 */
class Active extends Basics
{
    private $ActiveModel;
    private $ActiveTimeModel;

    /**
     * 构造方法
     * Active constructor.
     */
    public function __construct()
    {
        $this->ActiveModel = new ActiveModel;
        $this->ActiveTimeModel = new ActiveTimeModel;
    }

    /**
     * 获取秒杀活动会场首页数据
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getHallIndex()
    {
        // 获取秒杀首页顶部菜单
        $tabbar = $this->getActiveTabbar();
        if (empty($tabbar)) return ['tabbar' => [], 'goodsList' => []];
        // 获取活动商品
        $goodsList = $this->getGoodsListByActiveTimeId($tabbar[0]['active_time_id']);
        return compact('tabbar', 'goodsList');
    }

    /**
     * 获取秒杀活动组件数据
     * @param array $goodsParm
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getSharpModular($goodsParm = [])
    {
        // 获取秒杀活动列表
        $tabbar = $this->getActiveTabbar();
        if (empty($tabbar)) return ['active' => null, 'goodsList' => []];
        return [
            // 秒杀活动
            'active' => $tabbar[0],
            // 活动商品列表
            'goodsList' => $this->getGoodsListByActiveTimeId($tabbar[0]['active_time_id'], $goodsParm),
        ];
    }

    /**
     * 根据活动场次ID获取商品列表
     * @param int $activeTimeId
     * @param array $goodsParm
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getGoodsListByActiveTimeId($activeTimeId, $goodsParm = [])
    {
        return ActiveGoodsModel::getGoodsListByActiveTimeId($activeTimeId, $goodsParm);
    }

    /**
     * 获取活动商品详情
     * @param $activeTimeId
     * @param $sharpGoodsId
     * @return array|bool
     * @throws \think\exception\DbException
     */
    public function getyActiveGoodsDetail($activeTimeId, $sharpGoodsId)
    {
        // 活动详情
        $active = $this->getGoodsActive($activeTimeId, $sharpGoodsId);
        if (empty($active)) return false;
        // 商品详情
        $model = new ActiveGoodsModel;
        $goods = $model->getGoodsActiveDetail($active, $sharpGoodsId, true);
        if (empty($goods)) {
            $this->error = $model->getError();
            return false;
        }
        // 商品多规格信息
        $goods['goods_multi_spec'] = (new SharpGoodsModel)->getSpecData($goods);
        $specData = (new SharpGoodsModel)->getSpecData($goods);
        return compact('active', 'goods', 'specData');
    }

    /**
     * 获取订单提交的商品列表
     * @param $activeTimeId
     * @param $sharpGoodsId
     * @param $goodsSkuId
     * @param $goodsNum
     * @return array|bool
     * @throws \think\exception\DbException
     */
    public function getCheckoutGoodsList($activeTimeId, $sharpGoodsId, $goodsSkuId, $goodsNum)
    {
        // 活动详情
        $active = $this->getGoodsActive($activeTimeId, $sharpGoodsId);
        if (empty($active)) return false;
        // 商品详情
        $model = new ActiveGoodsModel;
        $goods = $model->getGoodsActiveDetail($active, $sharpGoodsId, false);
        if (empty($goods)) return false;
        // 商品sku信息
        $goods['goods_sku'] = GoodsModel::getGoodsSku($goods, $goodsSkuId);
        // 商品列表
        $goodsList = [$goods->hidden(['category', 'content', 'image', 'sku'])];
        foreach ($goodsList as &$item) {
            // 商品价格
            $item['goods_price'] = $item['goods_sku']['seckill_price'];
            $item['line_price'] = $item['goods_sku']['original_price'];
            // 商品id
            $item['spec_sku_id'] = $item['goods_sku']['spec_sku_id'];
            $item['goods_source_id'] = $item['sharp_goods_id'];
            // 商品购买数量
            $item['total_num'] = $goodsNum;
            // 商品购买总金额
            $item['total_price'] = helper::bcmul($item['goods_price'], $goodsNum);
        }
        return $goodsList;
    }

    /**
     * 活动详情
     * @param $activeTimeId
     * @param $sharpGoodsId
     * @return array|bool
     */
    private function getGoodsActive($activeTimeId, $sharpGoodsId)
    {
        // 获取活动商品的关联信息
        $model = $this->getActiveGoods($activeTimeId, $sharpGoodsId);
        if (empty($model) || !($model['active']['status'] && $model['active_time']['status'])) {
            $this->error = '很抱歉，该活动不存在或已结束';
            return false;
        }
        // 整理数据
        $startTime = $model['active']['active_date'] + ($model->active_time->getData('active_time') * 60 * 60);
        $endTime = $startTime + (1 * 60 * 60);
        $activeStatus = $this->getActivcGoodsStatus($startTime, $endTime);
        $data = [
            'active_id' => $model['active_id'],
            'active_time_id' => $model['active_time_id'],
            'active_time' => $model['active_time']['active_time'],
            'sales_actual' => $model['sales_actual'],
            'start_time' => $this->onFormatTime($startTime),
            'end_time' => $this->onFormatTime($endTime),
            'active_status' => $activeStatus,
            'count_down_time' => $this->getGoodsActiveCountDownTime($activeStatus, $startTime, $endTime),
            'wxapp_id' => $model['wxapp_id'],
        ];
        return $data;
    }

    /**
     * 获取活动商品的关联信息
     * @param $activeTimeId
     * @param $sharpGoodsId
     * @return mixed
     */
    public function getActiveGoods($activeTimeId, $sharpGoodsId)
    {
        static $data = [];
        if (!isset($data["{$activeTimeId}_{$sharpGoodsId}"])) {
            $model = ActiveGoodsModel::getGoodsActive($activeTimeId, $sharpGoodsId);
            !empty($model) && $data["{$activeTimeId}_{$sharpGoodsId}"] = $model;
        }
        return $data["{$activeTimeId}_{$sharpGoodsId}"];
    }

    /**
     * 活动商品倒计时
     * @param $activeStatus
     * @param $startTime
     * @param $endTime
     * @return bool|false|string
     */
    private function getGoodsActiveCountDownTime($activeStatus, $startTime, $endTime)
    {
        if ($activeStatus == GoodsStatusEnum::STATE_BEGIN) {
            return $this->onFormatTime($startTime);
        }
        if ($activeStatus == GoodsStatusEnum::STATE_SOON) {
            return $this->onFormatTime($endTime);
        }
        return false;
    }

    /**
     * 活动商品状态
     * @param $startTime
     * @param $endTime
     * @return int
     */
    private function getActivcGoodsStatus($startTime, $endTime)
    {
        $nowTime = time();
        if ($nowTime < $startTime) {
            return GoodsStatusEnum::STATE_SOON;
        }
        if ($nowTime >= $startTime && $nowTime < $endTime) {
            return GoodsStatusEnum::STATE_BEGIN;
        }
        return GoodsStatusEnum::STATE_END;
    }

    /**
     * 获取秒杀首页顶部菜单
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function getActiveTabbar()
    {
        // 当天的活动
        $todyActive = $this->ActiveModel->getNowActive();
        $data = [];
        if (!empty($todyActive)) {
            // 当前进行中的活动
            $data[] = $this->getBeginActive($todyActive);
            // 获取即将开始的活动
            $data = array_merge($data, $this->getSoonActive($todyActive));
        }
        // 获取预告的活动
        $data[] = $this->getNoticeActive();
        return array_values(array_filter($data));
    }

    /**
     * 获取当前进行中的活动
     * @param $todyActive
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function getBeginActive($todyActive)
    {
        // 当前的时间点
        $model = $this->ActiveTimeModel->getNowActiveTime($todyActive['active_id']);
        if (empty($model)) return [];
        // 整理数据
        $startTime = $todyActive['active_date'] + ($model->getData('active_time') * 60 * 60);
        $endTime = $startTime + (1 * 60 * 60);
        return [
            'active_id' => $todyActive['active_id'],
            'active_time_id' => $model['active_time_id'],
            'active_time' => $model['active_time'],
            'start_time' => $this->onFormatTime($startTime),
            'end_time' => $this->onFormatTime($endTime),
            'count_down_time' => $this->onFormatTime($endTime),
            'status' => ActiveStatusEnum::ACTIVE_STATE_BEGIN,
            'status_text' => '已开抢',
            'status_text2' => '正在疯抢',
            'sharp_modular_text' => '正在疯抢',
        ];
    }

    /**
     * 获取即将开始的活动
     * @param $todyActive
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function getSoonActive($todyActive)
    {
        // 当前的时间点
        $list = $this->ActiveTimeModel->getNextActiveTimes($todyActive['active_id']);
        if (empty($list) || $list->isEmpty()) return [];
        // 整理数据
        $data = [];
        foreach ($list as $item) {
            $startTime = $todyActive['active_date'] + ($item->getData('active_time') * 60 * 60);
            $endTime = $startTime + (1 * 60 * 60);
            $data[] = [
                'active_id' => $todyActive['active_id'],
                'active_time_id' => $item['active_time_id'],
                'active_time' => $item['active_time'],
                'start_time' => $this->onFormatTime($startTime),
                'end_time' => $this->onFormatTime($endTime),
                'count_down_time' => $this->onFormatTime($startTime),
                'status' => ActiveStatusEnum::ACTIVE_STATE_SOON,
                'status_text' => '即将开抢',
                'status_text2' => '即将开抢',
                'sharp_modular_text' => "{$item['active_time']} 场预告",
            ];
        }
        return $data;
    }

    /**
     * 获取预告的活动
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function getNoticeActive()
    {
        // 下一场活动
        $nextActive = $this->ActiveModel->getNextActive();
        if (empty($nextActive)) return [];
        // 第一个时间点
        $model = $this->ActiveTimeModel->getRecentActiveTime($nextActive['active_id']);
        if (empty($model)) return [];
        // 整理数据
        $startTime = $nextActive['active_date'] + ($model->getData('active_time') * 60 * 60);
        $endTime = $startTime + (1 * 60 * 60);
        return [
            'active_id' => $nextActive['active_id'],
            'active_time_id' => $model['active_time_id'],
            'active_time' => $model['active_time'],
            'start_time' => $this->onFormatTime($startTime),
            'end_time' => $this->onFormatTime($endTime),
            'count_down_time' => $this->onFormatTime($startTime),
            'status' => ActiveStatusEnum::ACTIVE_STATE_NOTICE,
            'status_text' => '预告',
            'status_text2' => $this->onFormatTime($startTime) . ' 开始',
            'sharp_modular_text' => $this->onFormatTime($startTime) . ' 开始',
        ];
    }

    /**
     * 将时间戳格式化为日期时间
     * @param $timeStamp
     * @return false|string
     */
    private function onFormatTime($timeStamp)
    {
        return date('Y-m-d H:i', $timeStamp);
    }

}