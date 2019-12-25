<?php

namespace app\api\model\bargain;

use app\common\model\bargain\Active as ActiveModel;
use app\api\model\bargain\Task as TaskModel;
use app\api\model\bargain\TaskHelp as TaskHelpModel;
use app\common\service\Goods as GoodsService;

/**
 * 砍价活动模型
 * Class Active
 * @package app\api\model\bargain
 */
class Active extends ActiveModel
{
    /**
     * 隐藏的字段
     * @var array
     */
    protected $hidden = [
        'peoples',
        'is_self_cut',
        'initial_sales',
        'actual_sales',
        'sort',
        'create_time',
        'update_time',
        'wxapp_id',
        'is_delete',
    ];

    /**
     * 获取器：分享标题
     * @param $value
     * @return mixed
     */
    public function getShareTitleAttr($value)
    {
        return htmlspecialchars_decode($value);
    }

    /**
     * 获取器：砍价助力语
     * @param $value
     * @return mixed
     */
    public function getPromptWordsAttr($value)
    {
        return htmlspecialchars_decode($value);
    }

    /**
     * 活动会场列表
     * @param array $param
     * @return mixed|\think\Paginator
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getHallList($param = [])
    {
        return $this->getList($param);
    }

    /**
     * 获取砍价活动列表（根据活动id集）
     * @param $activeIds
     * @param array $param
     * @return mixed|\think\Paginator
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getListByIds($activeIds, $param = [])
    {
        $this->where('active_id', 'in', $activeIds);
        return $this->getList($param);
    }

    /**
     * 获取砍价活动列表
     * @param $param
     * @return mixed|\think\Paginator
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function getList($param)
    {
        // 商品列表获取条件
        $params = array_merge([
            'status' => 1,         // 商品状态
            'sortType' => 'all',    // 排序类型
            'listRows' => 15,       // 每页数量
        ], $param);
        // 排序规则
        if ($params['sortType'] === 'all') {
            $this->order(['sort' => 'asc']);
        } elseif ($params['sortType'] === 'sales') {
            $this->order(['active_sales' => 'desc']);
        } elseif ($params['sortType'] === 'price') {
            $this->order(['floor_price' => $params['sortPrice'] ? 'desc' : 'asc']);
        }
        // 砍价活动列表
        $list = $this->field(['*', '(actual_sales + initial_sales) as active_sales'])
            ->where('start_time', '<=', time())
            ->where('end_time', '>=', time())
            ->where('status', '=', 1)
            ->where('is_delete', '=', 0)
            ->order(['sort' => 'asc'])
            ->paginate($params['listRows'], false, [
                'query' => \request()->request()
            ]);
        // 设置商品数据
        $list = GoodsService::setGoodsData($list);
        // 整理正在砍价的助力信息
        $list = $this->setHelpsData($list);
        return $list;
    }

    /**
     * 整理正在砍价的助力信息
     * @param $list
     * @return mixed
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function setHelpsData($list)
    {
        $model = new TaskHelpModel;
        foreach ($list as &$item) {
            $item['helps'] = $model->getHelpListByActiveId($item['active_id']);
            $item['helps_count'] = $model->getHelpCountByActiveId($item['active_id']);
        }
        return $list;
    }

    /**
     * 获取砍价活动详情
     * @param $activeId
     * @return Active|bool|null
     * @throws \think\exception\DbException
     */
    public function getDetail($activeId)
    {
        $model = static::detail($activeId);
        if (empty($model) || $model['is_delete'] == true || $model['status'] == false) {
            $this->error = '很抱歉，该砍价商品不存在或已下架';
            return false;
        }
        return $model;
    }

    /**
     * 获取用户是否正在参与改砍价活动，如果已参与则返回task_id
     * @param $activeId
     * @param bool $user
     * @return bool|int
     */
    public function getWhetherPartake($activeId, $user = false)
    {
        if ($user === false) {
            return false;
        }
        return TaskModel::getHandByUser($activeId, $user['user_id']);
    }

    /**
     * 累计活动销量(实际)
     * @return int|true
     * @throws \think\Exception
     */
    public function setIncSales()
    {
        return $this->setInc('actual_sales');
    }

}