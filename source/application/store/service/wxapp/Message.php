<?php

namespace app\store\service\wxapp;

use app\store\model\User as UserModel;
use app\store\model\Wxapp as WxappModel;
use app\common\library\wechat\WxTplMsg;
use app\common\service\wxapp\FormId as FormIdService;

/**
 * 推送模板消息服务类
 * Class Message
 * @package app\store\service\wxapp
 */
class Message
{
    // 分割符号
    const SEPARATOR = ',';

    /** @var array $stateSet 状态集 */
    private $stateSet = [];

    /** @var WxTplMsg $WxTplMsg 微信模板消息类 */
    private $WxTplMsg;

    /**
     * 构造方法
     * Message constructor.
     * @throws \app\common\exception\BaseException
     * @throws \think\exception\DbException
     */
    public function __construct()
    {
        // 实例化：微信模板消息类
        $config = WxappModel::getWxappCache();
        $this->WxTplMsg = new WxTplMsg($config['app_id'], $config['app_secret']);
    }

    /**
     * @param $data
     * @return bool
     * @throws \app\common\exception\BaseException
     * @throws \think\exception\DbException
     */
    public function send($data)
    {
        // 用户id集
        $userIdsArr = !strstr($data['user_id'], self::SEPARATOR) ? [$data['user_id']]
            : explode(self::SEPARATOR, $data['user_id']);
        // 批量发送
        foreach ($userIdsArr as $userId) {
            $this->sendTemplateMessage($userId, $data);
        }
        return true;
    }

    /**
     * 发送模板消息
     * @param $userId
     * @param $data
     * @return bool
     * @throws \app\common\exception\BaseException
     * @throws \think\exception\DbException
     */
    private function sendTemplateMessage($userId, $data)
    {
        // 获取formid
        if (!$formId = FormIdService::getAvailableFormId($userId)) {
            $this->recordState("用户[ID:$userId] 无可用formid，无法发送模板消息！");
            return false;
        }
        // 获取用户信息
        $user = UserModel::detail($data['user_id']);
        // 构建模板消息参数
        $params = [
            'touser' => $user['open_id'],
            'template_id' => $data['template_id'],
            'page' => $data['page'],
            'form_id' => $formId['form_id'],
            'data' => []
        ];
        // 格式化模板内容
        foreach (array_filter($data['content']) as $key => $item) {
            $params['data']['keyword' . ($key + 1)] = $item;
        }
        // 请求微信api：发送模板消息
        if ($status = $this->WxTplMsg->sendTemplateMessage($params)) {
            $this->recordState("用户[ID:$userId] 发送成功！");
        }
        // 标记formid已使用
        FormIdService::setIsUsed($formId['id']);
        return $status;
    }

    /**
     * 获取状态集
     * @return array
     */
    public function getStateSet()
    {
        return $this->stateSet;
    }

    /**
     * 记录状态集
     * @param $content
     */
    private function recordState($content)
    {
        $this->stateSet[] = $content;
    }

}