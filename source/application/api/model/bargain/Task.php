<?php

namespace app\api\model\bargain;

use app\api\model\Goods as GoodsModel;
use app\api\model\bargain\Active as ActiveModel;
use app\common\model\bargain\Task as TaskModel;
use app\api\model\bargain\TaskHelp as TaskHelpModel;
use app\api\service\bargain\Amount as AmountService;
use app\common\service\Goods as GoodsService;
use app\common\library\helper;

/**
 * 砍价任务模型
 * Class Task
 * @package app\api\model\bargain
 */
class Task extends TaskModel
{
    /**
     * 隐藏的字段
     * @var array
     */
    protected $hidden = [
        'peoples',
        'section',
        'is_delete',
        'wxapp_id',
        'create_time',
        'update_time',
    ];

    /**
     * 我的砍价列表
     * @param $userId
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public function getMyList($userId)
    {
        // 砍价活动列表
        $list = $this->where('user_id', '=', $userId)
            ->where('is_delete', '=', 0)
            ->order(['create_time' => 'desc'])
            ->paginate(5, false, [
                'query' => \request()->request()
            ]);
        // 设置商品数据
        $list = GoodsService::setGoodsData($list);
        return $list;
    }

    /**
     * 获取砍价任务详情
     * @param $taskId
     * @param bool $user
     * @return array|bool
     * @throws \think\exception\DbException
     */
    public function getTaskDetail($taskId, $user = false)
    {
        // 砍价任务详情
        $task = static::detail($taskId, ['user']);
        if (empty($task)) {
            $this->error = '砍价任务不存在';
            return false;
        }
        // 砍价活动详情
        $active = ActiveModel::detail($task['active_id']);
        // 砍价商品详情
        $goods = GoodsModel::detail($task['goods_id']);
        // 商品sku信息
        $goods['goods_sku'] = GoodsModel::getGoodsSku($goods, $task['spec_sku_id']);
        // 好友助力榜
        $help_list = TaskHelpModel::getListByTaskId($taskId);
        // 当前是否为发起人
        $is_creater = $this->isCreater($task, $user);
        // 当前是否已砍
        $is_cut = $this->isCut($help_list, $user);
        return compact('task', 'is_creater', 'is_cut', 'active', 'goods', 'help_list');
    }

    /**
     * 获取砍价任务的商品列表（用于订单结算）
     * @param $taskId
     * @return array|bool
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function getTaskGoods($taskId)
    {
        // 砍价任务详情
        $task = static::detail($taskId);
        if (empty($task) || $task['is_delete'] || $task['status'] == false) {
            $this->error = '砍价任务不存在或已结束';
            return false;
        }
        if ($task['is_buy'] == true) {
            $this->error = '该砍价商品已购买';
            return false;
        }
        // 砍价商品详情
        $goods = GoodsModel::detail($task['goods_id']);
        // 商品sku信息
        $goods['goods_sku'] = GoodsModel::getGoodsSku($goods, $task['spec_sku_id']);
        // 商品列表
        $goodsList = [$goods->hidden(['category', 'content', 'image', 'sku'])];
        foreach ($goodsList as &$item) {
            // 商品单价
            $item['goods_price'] = $task['actual_price'];
            // 商品购买数量
            $item['total_num'] = 1;
            $item['spec_sku_id'] = $item['goods_sku']['spec_sku_id'];
            // 商品购买总金额
            $item['total_price'] = $task['actual_price'];
        }
        return $goodsList;
    }

    /**
     * 订单创建后将砍价任务结束
     * @return false|int
     */
    public function setTaskEnd()
    {
        return $this->save(['status' => 0]);
    }

    /**
     * 获取用户是否正在参与改砍价活动，如果已参与则返回task_id
     * @param $activeId
     * @param $userId
     * @return bool|int
     */
    public static function getHandByUser($activeId, $userId)
    {
        $taskId = (new static)->where('active_id', '=', $activeId)
            ->where('user_id', '=', $userId)
            ->where('end_time', '>', time())
            ->where('status', '=', 1)
            ->where('is_delete', '=', 0)
            ->value('task_id');
        return $taskId ?: false;
    }

    /**
     * 新增砍价任务
     * @param $userId
     * @param $activeId
     * @param $goodsSkuId
     * @return bool
     * @throws \think\exception\DbException
     * @throws \Exception
     */
    public function partake($userId, $activeId, $goodsSkuId)
    {
        // 获取活动详情
        if (!$active = $this->getActiveDetail($activeId)) {
            return false;
        }
        // 验证能否创建砍价任务
        if (!$this->onVerify($active, $userId)) {
            return false;
        }
        // 获取商品详情
        $goods = GoodsModel::detail($active['goods_id']);
        // 商品sku信息
        $goods['goods_sku'] = GoodsModel::getGoodsSku($goods, $goodsSkuId);
        // 事务处理
        return $this->transaction(function () use ($userId, $active, $goodsSkuId, $goods) {
            // 创建砍价任务
            $this->add($userId, $active, $goodsSkuId, $goods);
            // 发起人自砍一刀
            $active['is_self_cut'] && $this->onCutEvent($userId, true);
            return true;
        });
    }

    /**
     * 帮砍一刀
     * @param $user
     * @return bool|false|int
     */
    public function helpCut($user)
    {
        // 好友助力榜
        $helpList = TaskHelpModel::getListByTaskId($this['task_id']);
        // 当前是否已砍
        if ($this->isCut($helpList, $user)) {
            $this->error = '您已参与砍价，请不要重复操作';
            return false;
        }
        // 帮砍一刀事件
        return $this->transaction(function () use ($user) {
            return $this->onCutEvent($user['user_id'], $this->isCreater($this, $user));
        });
    }

    /**
     * 砍一刀的金额
     * @return mixed
     */
    public function getCutMoney()
    {
        return $this['section'][$this['cut_people']];
    }

    /**
     * 帮砍一刀事件
     * @param $userId
     * @param bool $isCreater
     * @return false|int
     */
    private function onCutEvent($userId, $isCreater = false)
    {
        // 砍价金额
        $cutMoney = $this->getCutMoney();
        // 砍价助力记录
        $model = new TaskHelpModel;
        $model->add($this, $userId, $cutMoney, $isCreater);
        // 实际购买金额
        $actualPrice = helper::bcsub($this['actual_price'], $cutMoney);
        // 更新砍价任务信息
        $this->save([
            'cut_people' => ['inc', 1],
            'cut_money' => ['inc', $cutMoney],
            'actual_price' => $actualPrice,
            'is_floor' => helper::bcequal($actualPrice, $this['floor_price']),
        ]);
        return true;
    }

    /**
     * 创建砍价任务记录
     * @param $userId
     * @param $active
     * @param $goodsSkuId
     * @param $goods
     * @return false|int
     * @throws \Exception
     */
    private function add($userId, $active, $goodsSkuId, $goods)
    {
        // 分配砍价金额区间
        $section = $this->calcBargainSection(
            $goods['goods_sku']['goods_price'],
            $active['floor_price'],
            $active['peoples']
        );
        // 新增记录
        return $this->save([
            'active_id' => $active['active_id'],
            'user_id' => $userId,
            'goods_id' => $active['goods_id'],
            'spec_sku_id' => $goodsSkuId,
            'goods_price' => $goods['goods_sku']['goods_price'],
            'floor_price' => $active['floor_price'],
            'peoples' => $active['peoples'],
            'cut_people' => 0,
            'section' => $section,
            'cut_money' => 0.00,
            'actual_price' => $goods['goods_sku']['goods_price'],
            'end_time' => time() + ($active['expiryt_time'] * 3600),
            'is_buy' => 0,
            'status' => 1,
            'wxapp_id' => static::$wxapp_id,
        ]);
    }

    /**
     * 砍价任务标记为已购买
     * @return false|int
     */
    public function setIsBuy()
    {
        return $this->save(['is_buy' => 1]);
    }

    /**
     * 分配砍价金额区间
     * @param $goodsPrice
     * @param $floorPrice
     * @param $peoples
     * @return mixed
     * @throws \Exception
     */
    private function calcBargainSection($goodsPrice, $floorPrice, $peoples)
    {
        $AmountService = new AmountService(helper::bcsub($goodsPrice, $floorPrice), $peoples);
        return $AmountService->handle()['items'];
    }

    /**
     * 当前是否为发起人
     * @param $task
     * @param $user
     * @return bool
     */
    private function isCreater($task, $user)
    {
        if ($user === false) return false;
        return $user['user_id'] == $task['user_id'];
    }

    /**
     * 当前是否已砍
     * @param $helpList
     * @param $user
     * @return bool
     */
    private function isCut($helpList, $user)
    {
        if ($user === false) return false;
        foreach ($helpList as $item) {
            if ($item['user_id'] == $user['user_id']) return true;
        }
        return false;
    }

    /**
     * 获取活动详情
     * @param $activeId
     * @return Active|bool|null
     * @throws \think\exception\DbException
     */
    private function getActiveDetail($activeId)
    {
        // 获取活动详情
        $ActiveModel = new ActiveModel;
        $detail = $ActiveModel->getDetail($activeId);
        // 活动详情不存在
        if ($detail === false) {
            $this->error = $ActiveModel->getError();
            return false;
        }
        return $detail;
    }

    /**
     * 验证能否创建砍价任务
     * @param $active
     * @param $userId
     * @return bool
     */
    private function onVerify($active, $userId)
    {
        // 活动是否开始
        if (!$active['is_start']) {
            $this->error = '很抱歉，当前砍价活动未开始';
            return false;
        }
        // 活动是否到期合法
        if ($active['is_end']) {
            $this->error = '很抱歉，当前砍价活动已结束';
            return false;
        }
        // 判断当前用户是否已参加
        $taskId = static::getHandByUser($active['active_id'], $userId);
        if ($taskId !== false && $taskId > 0) {
            $this->error = '很抱歉，当前砍价活动您已参加，无需重复参与';
            return false;
        }
        return true;
    }

}