<?php

namespace app\store\model\bargain;

use app\common\model\bargain\Active as ActiveModel;
use app\store\service\Goods as GoodsService;

/**
 * 砍价活动模型
 * Class Active
 * @package app\store\model\bargain
 */
class Active extends ActiveModel
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
        ]);
        // 检索查询条件
        if (!empty($search)) {
            $this->where('goods.goods_name', 'like', "%{$search}%");
        }
        // 获取活动列表
        $list = $this->where("{$this->alias}.is_delete", '=', 0)
            ->order(["{$this->alias}.sort" => 'asc', "{$this->alias}.create_time" => 'desc'])
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
     * 新增记录
     * @param $data
     * @return bool|int
     */
    public function add($data)
    {
        if (!$this->onValidate($data, 'add')) {
            return false;
        }
        $data = $this->createData($data);
        return $this->allowField(true)->save($data) !== false;
    }

    /**
     * 更新记录
     * @param $data
     * @return bool|int
     */
    public function edit($data)
    {
        if (!$this->onValidate($data, 'edit')) {
            return false;
        }
        $data = $this->createData($data);
        return $this->allowField(true)->save($data) !== false;
    }

    private function createData($data)
    {
        $data['wxapp_id'] = static::$wxapp_id;
        $data['start_time'] = strtotime($data['start_time']);
        $data['end_time'] = strtotime($data['end_time']);
        return $data;
    }

    /**
     * 表单验证
     * @param $data
     * @param string $scene
     * @return bool
     */
    private function onValidate($data, $scene = 'add')
    {
        if ($scene === 'add') {
            if (!isset($data['goods_id']) || empty($data['goods_id'])) {
                $this->error = '请选择商品';
                return false;
            }
        }
        // 验证活动时间
        $data['start_time'] = strtotime($data['start_time']);
        $data['end_time'] = strtotime($data['end_time']);
        if ($data['end_time'] <= $data['start_time']) {
            $this->error = '活动结束时间必须大于开始时间';
            return false;
        }
        return true;
    }

    /**
     * 软删除
     * @return false|int
     */
    public function setDelete()
    {
        return $this->allowField(true)->save(['is_delete' => 1]);
    }

    /**
     * 商品ID是否存在
     * @param $goodsId
     * @return bool
     */
    public static function isExistGoodsId($goodsId)
    {
        return !!(new static)->where('goods_id', '=', $goodsId)
            ->where('is_delete', '=', 0)
            ->value('active_id');
    }

}