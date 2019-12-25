<?php

namespace app\store\model\bargain;

use app\common\model\bargain\Task as TaskModel;
use app\store\service\Goods as GoodsService;

/**
 * 砍价任务模型
 * Class Task
 * @package app\store\model\bargain
 */
class Task extends TaskModel
{
    /**
     * 获取列表数据
     * @param string $search
     * @return mixed|\think\Paginator
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getList($search = '')
    {
        $this->setBaseQuery($this->alias, [
            ['goods', 'goods_id'],
            ['user', 'user_id'],
        ]);
        // 检索查询条件
        if (!empty($search)) {
            $this->where(function ($query) use ($search) {
                $query->whereOr('goods.goods_name', 'like', "%{$search}%");
                $query->whereOr('user.nickName', 'like', "%{$search}%");
            });
        }
        // 获取活动列表
        $list = $this->with(['user'])
            ->where("{$this->alias}.is_delete", '=', 0)
            ->order(["{$this->alias}.create_time" => 'desc'])
            ->paginate(15, false, [
                'query' => \request()->request()
            ]);
        if (!$list->isEmpty()) {
            // 设置商品数据
            $list = GoodsService::setGoodsData($list);
        }
        return $list;
    }

    /**
     * 软删除
     * @return false|int
     */
    public function setDelete()
    {
        return $this->allowField(true)->save(['is_delete' => 1]);
    }

}