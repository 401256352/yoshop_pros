<div class="row-content am-cf">
    <div class="row">
        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <div id="app" v-cloak class="widget am-cf">
                <form id="my-form" class="am-form tpl-form-line-form" method="post">
                    <div class="widget-body">
                        <fieldset>
                            <div class="widget-head am-cf">
                                <div class="widget-title am-fl"> 编辑活动场次</div>
                            </div>
                            <div class="am-form-group am-padding-top">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require"> 活动日期 </label>
                                <div class="am-u-sm-9 am-u-md-6 am-u-lg-5 am-u-end">
                                    <div class="am-form--static"><?= $model['active']['active_date'] ?></div>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require"> 活动场次 </label>
                                <div class="am-u-sm-9 am-u-md-6 am-u-lg-5 am-u-end">
                                    <div class="am-form--static"><?= $model['active_time'] ?></div>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require"> 选择商品 </label>
                                <div class="am-u-sm-9 am-u-md-6 am-u-lg-8 am-u-end">
                                    <div class="am-form-file am-margin-top-xs">
                                        <button type="button"
                                                class="upload-file am-btn am-btn-secondary am-radius"
                                                @click.stop="onSelectGoods">
                                            <i class="am-icon-cloud-upload"></i> 选择秒杀商品
                                        </button>
                                        <div v-if="goodsList.length > 0" class="widget-goods-list am-padding-top">
                                            <table width="100%"
                                                   class="am-table am-table-compact tpl-table-black am-text-nowrap">
                                                <thead>
                                                <tr>
                                                    <th>商品ID</th>
                                                    <th>商品图片</th>
                                                    <th>商品名称</th>
                                                    <th>操作</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr v-for="(item, index) in goodsList">
                                                    <td class="am-text-middle">
                                                        <input type="hidden" name="active[sharp_goods][]"
                                                               :value="item.goods_id">
                                                        {{ item.goods_id }}
                                                    </td>
                                                    <td class="am-text-middle">
                                                        <a :href="item.goods_image"
                                                           title="点击查看大图" target="_blank">
                                                            <img :src="item.goods_image"
                                                                 width="50" height="50" alt="商品图片">
                                                        </a>
                                                    </td>
                                                    <td class="am-text-middle">
                                                        <p class="item-title">{{ item.goods_name }}</p>
                                                    </td>
                                                    <td class="am-text-middle">
                                                        <a href="javascript:void(0);"
                                                           @click.stop="onDeleteGoods(index)">删除</a>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="am-form-group">
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

<script src="assets/common/js/vue.min.js?v=<?= $version ?>"></script>
<script src="assets/store/js/select.data.js?v=<?= $version ?>"></script>
<script>
    $(function () {

        new Vue({
            el: '#app',
            data: {
                // 商品列表
                goodsList: <?= \app\common\library\helper::jsonEncode($goodsList) ?>
            },
            methods: {
                // 选择商品
                onSelectGoods: function () {
                    var app = this;
                    $.selectData({
                        title: '选择商品',
                        uri: 'sharp.goods/lists&status=10',
                        duplicate: false,
                        dataIndex: 'goods_id',
                        done: function (data) {
                            data.forEach(function (item) {
                                app.goodsList.push(item);
                            });
                        },
                        getExistData: function () {
                            var goodsIds = [];
                            app.goodsList.forEach(function (item) {
                                goodsIds.push(item.goods_id);
                            });
                            return goodsIds;
                        }
                    });
                },
                // 删除商品
                onDeleteGoods: function (index) {
                    var app = this;
                    return app.goodsList.splice(index, 1);
                }
            }
        });

        /**
         * 表单验证提交
         * @type {*}
         */
        $('#my-form').superForm();

    });
</script>
