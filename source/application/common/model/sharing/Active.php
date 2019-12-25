<?php

namespace app\common\model\sharing;

use think\Hook;
use app\common\model\BaseModel;
use app\common\service\Message as MessageService;

/**
 * 拼团拼单模型
 * Class Active
 * @package app\common\model\sharing
 */
class Active extends BaseModel
{
    protected $name = 'sharing_active';
    protected $append = ['surplus_people'];

    /**
     * 拼团拼单模型初始化
     */
    public static function init()
    {
        parent::init();
        // 监听订单处理事件
        $static = new static;
        Hook::listen('sharing_active', $static);
    }

    /**
     * 获取器：拼单状态
     * @param $value
     * @return array
     */
    public function getStatusAttr($value)
    {
        $state = [
            0 => '未拼单',
            10 => '拼单中',
            20 => '拼单成功',
            30 => '拼单失败',
        ];
        return ['text' => $state[$value], 'value' => $value];
    }

    /**
     * 获取器：结束时间
     * @param $value
     * @return array
     */
    public function getEndTimeAttr($value)
    {
        return ['text' => date('Y-m-d H:i:s', $value), 'value' => $value];
    }

    /**
     * 获取器：剩余拼团人数
     * @param $value
     * @return array
     */
    public function getSurplusPeopleAttr($value, $data)
    {
        return $data['people'] - $data['actual_people'];
    }

    /**
     * 关联拼团商品表
     * @return \think\model\relation\BelongsTo
     */
    public function goods()
    {
        return $this->belongsTo('Goods');
    }

    /**
     * 关联用户表（团长）
     * @return \think\model\relation\BelongsTo
     */
    public function user()
    {
        $module = self::getCalledModule() ?: 'common';
        return $this->belongsTo("app\\{$module}\\model\\User", 'creator_id');
    }

    /**
     * 关联拼单成员表
     * @return \think\model\relation\HasMany
     */
    public function users()
    {
        return $this->hasMany('ActiveUsers', 'active_id')
            ->order(['is_creator' => 'desc', 'create_time' => 'asc']);
    }

    /**
     * 拼单详情
     * @param $active_id
     * @param array $with
     * @return static|null
     * @throws \think\exception\DbException
     */
    public static function detail($active_id, $with = [])
    {
        return static::get($active_id, array_merge(['goods', 'users' => ['user', 'sharingOrder']], $with));
    }

    /**
     * 验证当前拼单是否允许加入新成员
     * @return bool
     */
    public function checkAllowJoin()
    {
        if (!in_array($this['status']['value'], [0, 10])) {
            $this->error = '当前拼单已结束';
            return false;
        }
        if (time() > $this['end_time']) {
            $this->error = '当前拼单已结束';
            return false;
        }
        if ($this['actual_people'] >= $this['people']) {
            $this->error = '当前拼单人数已满';
            return false;
        }
        return true;
    }

    /**
     * 新增拼单记录
     * @param $creator_id
     * @param $order_id
     * @param OrderGoods $goods
     * @return false|int
     */
    public function onCreate($creator_id, $order_id, $goods)
    {
        // 新增拼单记录
        $this->save([
            'goods_id' => $goods['goods_id'],
            'people' => $goods['people'],
            'actual_people' => 1,
            'creator_id' => $creator_id,
            'end_time' => time() + ($goods['group_time'] * 60 * 60),
            'status' => 10,
            'wxapp_id' => $goods['wxapp_id']
        ]);
        // 新增拼单成员记录
        ActiveUsers::add([
            'active_id' => $this['active_id'],
            'order_id' => $order_id,
            'user_id' => $creator_id,
            'is_creator' => 1,
            'wxapp_id' => $goods['wxapp_id']
        ]);
        return true;
    }

    /**
     * 更新拼单记录
     * @param $user_id
     * @param $order_id
     * @return bool
     * @throws \app\common\exception\BaseException
     * @throws \think\exception\DbException
     */
    public function onUpdate($user_id, $order_id)
    {
        // 验证当前拼单是否允许加入新成员
        if (!$this->checkAllowJoin()) {
            return false;
        }
        // 新增拼单成员记录
        ActiveUsers::add([
            'active_id' => $this['active_id'],
            'order_id' => $order_id,
            'user_id' => $user_id,
            'is_creator' => 0,
            'wxapp_id' => $this['wxapp_id']
        ]);
        // 累计已拼人数
        $actual_people = $this['actual_people'] + 1;
        // 更新拼单记录：当前已拼人数、拼单状态
        $status = $actual_people >= $this['people'] ? 20 : 10;
        $this->save([
            'actual_people' => $actual_people,
            'status' => $status
        ]);
        // 拼单成功, 发送模板消息
        if ($status == 20) {
            $model = static::detail($this['active_id']);
            (new MessageService)->sharingActive($model, '拼团成功');
        }
        return true;
    }

}
