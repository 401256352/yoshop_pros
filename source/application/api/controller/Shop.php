<?php

namespace app\api\controller;

use app\api\model\store\Shop as ShopModel;


/**
 * 门店列表
 * Class Shop
 * @package app\api\controller
 */
class Shop extends Controller
{
    
    public function index()
    {
        
        // 当前用户信息
        $userInfo = $this->getUser();
        
        $user_id=$userInfo['user_id'];
        
        
        
        $model = new ShopModel;
//         $list = $model->getList(true, $longitude, $latitude);
        $list = $model->getListByUserId($user_id);
        
        if(count($list)<1){
            
            return $this->renderError("用户未绑定商户，请联系客服");
        }
      
        $list=$list[0];
        $list['todayEstimateSelfncome']=11; //今日销售额(元)
        $list['theMonthEstimateSelfIncome']=12; //本月销售额(元)
        $list['todayEstimateIncome']=13; //今日营收(元)
        $list['theMonthEstimateIncome']=13; //本月营收(元)
        $list['waitSendOrderCount']=14; //待发货订单
        $list['todayTradeOrderCount']=15; //今日成交订单
        $list['billableAmount']=16; //可提现金额
        $list['billableAmount']=17; //可提现金额
        return $this->renderSuccess(compact('list'));
    }
    /**
     * 门店列表
     * @param string $longitude
     * @param string $latitude
     * @return array
     * @throws \think\exception\DbException
     */
    public function lists($longitude = '', $latitude = '')
    {
        $model = new ShopModel;
        $list = $model->getList(true, $longitude, $latitude);
        return $this->renderSuccess(compact('list'));
    }

    /**
     * 门店详情
     * @param $shop_id
     * @return array
     * @throws \think\exception\DbException
     */
    public function detail($shop_id)
    {
        $detail = ShopModel::detail($shop_id);
        return $this->renderSuccess(compact('detail'));
    }

}