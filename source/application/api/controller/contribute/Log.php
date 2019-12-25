<?php

namespace app\api\controller\contribute;

use app\api\controller\Controller;
use app\api\model\user\ContributeLog as ContributeLogModel;
use app\store\model\Setting as SettingModel;

use app\common\model\user\ContributeLog;

use app\api\model\User as UserModel;

use think\Db;

use app\common\model\user\BalanceLog as BalanceLogModel;

use app\common\model\user\PointsLog as PointsLogModel;


/**
 * 余额账单明细
 * Class Log
 * @package app\api\controller\balance
 */
class Log extends Controller
{
    /**
     * 积分明细列表
     * @return array
     * @throws \app\common\exception\BaseException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $user = $this->getUser();
//         usertask($user['user_id'],3); //每日签到
        $list = (new ContributeLogModel)->getList($user['user_id']);
        return $this->renderSuccess(compact('list'));
    }

    /**
     * 分销贡献转换比例设置
     * @return array
     * @throws \app\common\exception\BaseException
     * @throws \think\exception\DbException
     */
    public function setting()
    {
        $user = $this->getUser();
        //         usertask($user['user_id'],3); //每日签到
        $list = (new ContributeLogModel)->getList($user['user_id']);
        
        $vars['values'] = SettingModel::getItem('trade');
        
        return $this->renderSuccess($vars);
    }
    
    /**
     * 确认兑换
     * @param null $planId
     * @param int $customMoney
     * @return array
     * @throws \app\common\exception\BaseException
     * @throws \think\exception\DbException
     */
    public function submit( $customMoney = 0)
    {
        // 用户信息
        $userInfo = $this->getUser();
        
        $trade = SettingModel::getItem('trade');
        
        $rate=$trade['rate'];
        
        if($customMoney<$rate['zong'])
        {
            return $this->renderError('兑换失败,兑换贡献值不能小于'.$rate['zong']);
        }
        
        if($userInfo['contribute']<$rate['zong'])
        {
            return $this->renderError('兑换失败,贡献值未达标');
        }
        
        $userdata['contribute']=$userInfo['contribute']-$customMoney;
        $userdata['balance']=$userInfo['balance']+($customMoney*($rate['money']/100));
        $userdata['points']=$userInfo['points']+($customMoney*($rate['points']/100));
        
        $where['user_id']=$userInfo['user_id'];
        
        $useres=db('user')->where($where)->update($userdata);
        
        if(!$useres)
        {
            return $this->renderError('兑换失败, 系统错误，请重试');
        }
        
        $data['user_id']=$userInfo['user_id'];
        $data['value']=$customMoney*-1;
        $data['describe']="贡献值兑换";
        $data['remark']="贡献值兑换";
        $data['create_time']=time();
//         $data['wxapp_id']="贡献值兑换";
        $data['user_id']=$userInfo['user_id'];
        
        $res=(new ContributeLogModel)->add($data);
        
        $bdata['user_id']=$userInfo['user_id'];
        $bdata['scene']=50;
        $bdata['money']=($customMoney*($rate['money']/100));
        $bdata['describe']="贡献值兑换收入";
        $bdata['remark']="贡献值兑换收入，比例".$rate['money']."%";
        $bdata['create_time']=time();
        //         $data['wxapp_id']="贡献值兑换";
        $bdata['user_id']=$userInfo['user_id'];
        
        $bres=(new BalanceLogModel)->add($bdata['scene'],$bdata, $bdata['describe']);
        
        $pdata['user_id']=$userInfo['user_id'];
        $pdata['value']=($customMoney*($rate['points']/100));
        $pdata['describe']="贡献值兑换收入";
        $pdata['remark']="贡献值兑换收入，比例".$rate['points']."%";
        $pdata['create_time']=time();
        //         $data['wxapp_id']="贡献值兑换";
        $pdata['user_id']=$userInfo['user_id'];
        
        $pres=(new PointsLogModel)->add($pdata);
        
        
        // 生成充值订单
     /*    $model = new OrderModel;
        if (!$model->createOrder($userInfo, $planId, $customMoney)) {
            return $this->renderError($model->getError() ?: '充值失败');
        }
        // 构建微信支付
        $payment = PaymentService::wechat(
            $userInfo,
            $model['order_id'],
            $model['order_no'],
            $model['pay_price'],
            OrderTypeEnum::RECHARGE
            );
         */
        // 充值状态提醒
        $message = ['success' => '充值成功', 'error' => '订单未支付'];
        return $this->renderSuccess(compact('payment', 'message'), $message);
    }
    /*
     * 每日签到
     * */
    public function signin()
    {
       $user = $this->getUser();
       $res= usertask($user['user_id'],3); //每日签到
       
       if($res==1)
       {
           return $this->renderSuccess(null,"签到成功");
       }
       if($res==0)
       {
           return $this->renderError("该任务以下架");
       }
       
       if($res==-1)
       {
           return $this->renderError("系统错误，请重试");
       }
       if($res==2)
       {
           return $this->renderError("已签到，请勿重复签到");
       }
       
    }
}