<div class="row-content am-cf">
    <div class="row">
        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <div class="widget am-cf">
                <form id="my-form" class="am-form tpl-form-line-form" method="post">
                    <div class="widget-body">
                        <fieldset>
                            <div class="widget-head am-cf">
                                <div class="widget-title am-fl"> 新增砍价活动</div>
                            </div>

                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require"> 选择商品 </label>
                                <div class="am-u-sm-9 am-u-md-6 am-u-lg-5 am-u-end">
                                    <div class="am-form-file am-margin-top-xs">
                                        <button type="button"
                                                class="j-selectGoods upload-file am-btn am-btn-secondary am-radius">
                                            <i class="am-icon-cloud-upload"></i> 选择商品
                                        </button>
                                        <div class="widget-goods-list uploader-list am-cf">
                                        </div>
                                    </div>
                                    <div class="help-block">
                                        <small>注：砍价活动仅支持单规格商品 或 同价的多规格商品</small>
                                    </div>
                                </div>
                            </div>

                            <div class="am-form-group am-padding-top">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require"> 活动时间 </label>
                                <div class="am-u-sm-9 am-u-md-6 am-u-lg-5 am-u-end">
                                    <!-- 会员等级列表-->
                                    <div class="am-input-group">
                                        <input type="text" class="j-laydate-start am-form-field"
                                               name="active[start_time]"
                                               placeholder="开始时间"
                                               required>
                                        <span class="am-input-group-label am-input-group-label__center">至</span>
                                        <input type="text" class="j-laydate-end am-form-field"
                                               name="active[end_time]"
                                               placeholder="结束时间"
                                               required>
                                    </div>
                                    <div class="help-block">
                                        <small>砍价活动的开始日期与截止日期</small>
                                    </div>
                                </div>
                            </div>

                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require"> 砍价有效期 </label>
                                <div class="am-u-sm-9 am-u-md-6 am-u-lg-5 am-u-end">
                                    <input type="number" min="1" class="tpl-form-input" name="active[expiryt_time]"
                                           placeholder="" required>
                                    <small>自用户发起砍价到砍价截止的时间，单位：小时</small>
                                </div>
                            </div>

                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require"> 砍价底价 </label>
                                <div class="am-u-sm-9 am-u-md-6 am-u-lg-5 am-u-end">
                                    <input type="number" min="0.01" class="tpl-form-input" name="active[floor_price]"
                                           placeholder="" required>
                                    <small>砍价商品的最低价格，单位：元</small>
                                </div>
                            </div>

                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require"> 帮砍人数 </label>
                                <div class="am-u-sm-9 am-u-md-6 am-u-lg-5 am-u-end">
                                    <input type="number" min="1" class="tpl-form-input" name="active[peoples]"
                                           placeholder="" required>
                                    <small>每个砍价订单的帮砍人数，达到该人数才可砍至底价</small>
                                </div>
                            </div>

                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require"> 可自砍一刀 </label>
                                <div class="am-u-sm-9 am-u-md-6 am-u-lg-5 am-u-end">
                                    <label class="am-radio-inline">
                                        <input type="radio" name="active[is_self_cut]" value="1" data-am-ucheck checked>
                                        允许
                                    </label>
                                    <label class="am-radio-inline">
                                        <input type="radio" name="active[is_self_cut]" value="0" data-am-ucheck>
                                        不允许
                                    </label>
                                    <div class="help-block">
                                        <small>砍价发起人自己砍一刀</small>
                                    </div>
                                </div>
                            </div>

                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require"> 必须底价购买 </label>
                                <div class="am-u-sm-9 am-u-md-6 am-u-lg-5 am-u-end">
                                    <label class="am-radio-inline">
                                        <input type="radio" name="active[is_floor_buy]" value="1" data-am-ucheck>
                                        是
                                    </label>
                                    <label class="am-radio-inline">
                                        <input type="radio" name="active[is_floor_buy]" value="0" data-am-ucheck
                                               checked>
                                        否
                                    </label>
                                    <div class="help-block">
                                        <small>只有砍到底价才可以购买</small>
                                    </div>
                                </div>
                            </div>

                            <div class="am-form-group am-padding-top">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require"> 初始销量 </label>
                                <div class="am-u-sm-9 am-u-md-6 am-u-lg-5 am-u-end">
                                    <input type="number" min="0" class="tpl-form-input" name="active[initial_sales]"
                                           required>
                                    <small>注：前台展示的销量 = 初始销量 + 实际销量</small>
                                </div>
                            </div>

                            <div class="am-form-group am-padding-top">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require"> 分享标题 </label>
                                <div class="am-u-sm-9 am-u-md-6 am-u-lg-5 am-u-end">
                                    <input type="text" class="tpl-form-input" name="active[share_title]"
                                           value="麻烦帮我砍一刀！我真的很想要了，爱你哟！(๑′ᴗ‵๑)" required>
                                </div>
                            </div>

                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label"> 砍价助力语 </label>
                                <div class="am-u-sm-9 am-u-md-6 am-u-lg-5 am-u-end">
                                    <input type="text" class="tpl-form-input" name="active[prompt_words]"
                                           value="&#34;朋友一生一起走，帮砍一刀有没有&#34;">
                                </div>
                            </div>

                            <div class="am-form-group am-padding-top">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require"> 活动状态 </label>
                                <div class="am-u-sm-9 am-u-md-6 am-u-lg-5 am-u-end">
                                    <label class="am-radio-inline">
                                        <input type="radio" name="active[status]" value="1" data-am-ucheck
                                               checked>
                                        启用
                                    </label>
                                    <label class="am-radio-inline">
                                        <input type="radio" name="active[status]" value="0" data-am-ucheck>
                                        禁用
                                    </label>
                                </div>
                            </div>

                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">排序 </label>
                                <div class="am-u-sm-9 am-u-md-6 am-u-lg-5 am-u-end">
                                    <input type="number" min="0" class="tpl-form-input" name="active[sort]"
                                           value="100" required>
                                    <small>数字越小越靠前</small>
                                </div>
                            </div>

                            <div class="am-form-group">
                                <div class="am-u-sm-9 am-u-sm-push-3 am-margin-top-lg">
                                    <button type="submit" class="j-submit am-btn am-btn-sm am-btn-secondary"> 提交
                                    </button>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- 商品列表 -->
<script id="tpl-goods-list-item" type="text/template">
    {{ each $data }}
    <div class="file-item">
        <a href="{{ $value.image }}" title="{{ $value.goods_name }}" target="_blank">
            <img src="{{ $value.image }}">
        </a>
        <input type="hidden" name="active[goods_id]" value="{{ $value.goods_id }}">
    </div>
    {{ /each }}
</script>

<script src="assets/common/plugins/laydate/laydate.js"></script>
<script src="assets/store/js/select.data.js?v=<?= $version ?>"></script>
<script>
    $(function () {

        // 选择商品
        var $goodsList = $('.widget-goods-list');
        $('.j-selectGoods').selectData({
            title: '选择商品',
            uri: 'goods/lists',
            dataIndex: 'goods_id',
            done: function (data) {
                data = [data[0]];
                var $html = $(template('tpl-goods-list-item', data));
                $goodsList.html($html);
            }
        });

        // 时间选择器
        laydate.render({
            elem: '.j-laydate-start'
            , type: 'datetime'
        });

        // 时间选择器
        laydate.render({
            elem: '.j-laydate-end'
            , type: 'datetime'
        });

        /**
         * 表单验证提交
         * @type {*}
         */
        $('#my-form').superForm();

    });
</script>
