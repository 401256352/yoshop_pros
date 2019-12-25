<?php

namespace app\api\controller\contribute;

use app\api\controller\Controller;
use app\api\model\user\ContributeLog as ContributeLogModel;

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