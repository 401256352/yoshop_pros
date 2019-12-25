<?php

namespace app\store\service\order;

use app\store\model\OrderAddress as OrderAddressModel;

/**
 * 订单导出服务类
 * Class Export
 * @package app\store\service\order
 */
class Export
{
    /**
     * 表格标题
     * @var array
     */
    private $tileArray = [
        '订单号', '商品信息', '订单总额', '优惠券抵扣', '积分抵扣', '运费金额', '后台改价', '实付款金额', '支付方式', '下单时间',
        '买家', '买家留言', '配送方式', '自提门店名称', '自提联系人', '自提联系电话', '收货人姓名', '联系电话', '收货人地址',
        '物流公司', '物流单号', '付款状态', '付款时间', '发货状态', '发货时间', '收货状态', '收货时间', '订单状态', '微信支付交易号', '是否已评价'
    ];

    /**
     * 订单导出
     * @param $list
     */
    public function orderList($list)
    {
        // 表格内容
        $dataArray = [];
        foreach ($list as $order) {
            /* @var OrderAddressModel $address */
            $address = $order['address'];
            $dataArray[] = [
                '订单号' => $this->filterValue($order['order_no']),
                '商品信息' => $this->filterGoodsInfo($order),
                '订单总额' => $this->filterValue($order['total_price']),
                '优惠券抵扣' => $this->filterValue($order['coupon_money']),
                '积分抵扣' => $this->filterValue($order['points_money']),
                '运费金额' => $this->filterValue($order['express_price']),
                '后台改价' => $this->filterValue("{$order['update_price']['symbol']}{$order['update_price']['value']}"),
                '实付款金额' => $this->filterValue($order['pay_price']),
                '支付方式' => $this->filterValue($order['pay_type']['text']),
                '下单时间' => $this->filterValue($order['create_time']),
                '买家' => $this->filterValue($order['user']['nickName']),
                '买家留言' => $this->filterValue($order['buyer_remark']),
                '配送方式' => $this->filterValue($order['delivery_type']['text']),
                '自提门店名称' => !empty($order['extract_shop']) ? $this->filterValue($order['extract_shop']['shop_name']) : '',
                '自提联系人' => !empty($order['extract']) ? $this->filterValue($order['extract']['linkman']) : '',
                '自提联系电话' => !empty($order['extract']) ? $this->filterValue($order['extract']['phone']) : '',
                '收货人姓名' => $this->filterValue($order['address']['name']),
                '联系电话' => $this->filterValue($order['address']['phone']),
                '收货人地址' => $this->filterValue($address ? $address->getFullAddress() : ''),
                '物流公司' => $this->filterValue($order['express']['express_name']),
                '物流单号' => $this->filterValue($order['express_no']),
                '付款状态' => $this->filterValue($order['pay_status']['text']),
                '付款时间' => $this->filterTime($order['pay_time']),
                '发货状态' => $this->filterValue($order['delivery_status']['text']),
                '发货时间' => $this->filterTime($order['delivery_time']),
                '收货状态' => $this->filterValue($order['receipt_status']['text']),
                '收货时间' => $this->filterTime($order['receipt_time']),
                '订单状态' => $this->filterValue($order['order_status']['text']),
                '微信支付交易号' => $this->filterValue($order['transaction_id']),
                '是否已评价' => $this->filterValue($order['is_comment'] ? '是' : '否'),
            ];
        }
        // 导出csv文件
        $filename = 'order-' . date('YmdHis');
        return export_excel($filename . '.csv', $this->tileArray, $dataArray);
    }

    /**
     * 批量发货模板
     */
    public function deliveryTpl()
    {
        // 导出csv文件
        $filename = 'delivery-' . date('YmdHis');
        return export_excel($filename . '.csv', ['订单号', '物流单号']);
    }

    /**
     * 格式化商品信息
     * @param $order
     * @return string
     */
    private function filterGoodsInfo($order)
    {
        $content = '';
        foreach ($order['goods'] as $key => $goods) {
            $content .= ($key + 1) . ".商品名称：{$goods['goods_name']}\n";
            !empty($goods['goods_attr']) && $content .= "　商品规格：{$goods['goods_attr']}\n";
            $content .= "　购买数量：{$goods['total_num']}\n";
            $content .= "　商品总价：{$goods['total_price']}元\n\n";
        }
        return $content;
    }

    /**
     * 表格值过滤
     * @param $value
     * @return string
     */
    private function filterValue($value)
    {
        return "\t" . $value . "\t";
    }

    /**
     * 日期值过滤
     * @param $value
     * @return string
     */
    private function filterTime($value)
    {
        if (!$value) return '';
        return $this->filterValue(date('Y-m-d H:i:s', $value));
    }

}