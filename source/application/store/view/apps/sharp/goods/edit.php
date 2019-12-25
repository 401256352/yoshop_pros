<link rel="stylesheet" href="assets/store/css/goods.css?v=<?= $version ?>">
<div class="row-content am-cf">
    <div class="row">
        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <div class="widget am-cf">
                <form id="my-form" class="am-form tpl-form-line-form">
                    <div class="widget-body">
                        <fieldset>
                            <div class="widget-head am-cf">
                                <div class="widget-title am-fl">编辑秒杀商品</div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require"> 商品信息 </label>
                                <div class="am-u-sm-9 am-u-md-6 am-u-lg-5 am-u-end">
                                    <div class="goods-detail">
                                        <div class="goods-image">
                                            <img src="<?= $goods['goods_image'] ?>" alt="">
                                        </div>
                                        <div class="goods-info dis-flex flex-dir-column flex-x-center">
                                            <p class="goods-title"><?= $goods['goods_name'] ?></p>
                                            <p class="goods-title">ID：<?= $goods['goods_id'] ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 商品单规格 -->
                            <?php if ($goods['spec_type'] == 10): ?>
                                <div class="goods-spec-single"
                                     style="display: <?= $goods['spec_type'] == 10 ? 'block' : 'none' ?>;">
                                    <div class="am-form-group">
                                        <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require"> 商品编码 </label>
                                        <div class="am-u-sm-9 am-u-md-6 am-u-lg-5 am-u-end">
                                            <div class="am-form--static"><?= $goods['goods_sku']['goods_no'] ?: '--' ?></div>
                                        </div>
                                    </div>
                                    <div class="am-form-group">
                                        <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require"> 商品售价 </label>
                                        <div class="am-u-sm-9 am-u-md-6 am-u-lg-5 am-u-end">
                                            <div class="am-form--static"><?= $goods['goods_sku']['goods_price'] ?></div>
                                        </div>
                                    </div>
                                    <div class="am-form-group am-padding-top">
                                        <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require"> 秒杀价格 </label>
                                        <div class="am-u-sm-9 am-u-md-6 am-u-lg-5 am-u-end">
                                            <input type="number" min="0" class="tpl-form-input"
                                                   name="goods[sku][seckill_price]"
                                                   value="<?= $model['goods_sku']['seckill_price'] ?>" required>
                                        </div>
                                    </div>
                                    <div class="am-form-group">
                                        <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require"> 秒杀库存数量 </label>
                                        <div class="am-u-sm-9 am-u-md-6 am-u-lg-5 am-u-end">
                                            <input type="number" min="0" class="tpl-form-input"
                                                   name="goods[sku][seckill_stock]"
                                                   value="<?= $model['goods_sku']['seckill_stock'] ?>" required>
                                            <div class="help-block">
                                                <small>注：秒杀库存为独立库存，与主商品库存不同步</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- 商品多规格 -->
                            <?php if ($goods['spec_type'] == 20): ?>
                                <div class="am-form-group am-padding-top">
                                    <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">商品规格 </label>
                                    <div id="many-app" v-cloak class="goods-spec-many am-u-sm-9 am-u-end"
                                         style="display: block">
                                        <div class="goods-spec-box style-simplify">
                                            <!-- 商品多规格sku信息 -->
                                            <div v-if="spec_list.length > 0" class="goods-sku am-scrollable-horizontal">
                                                <!-- sku 批量设置 -->
                                                <div class="spec-batch am-form-inline">
                                                    <div class="am-form-group">
                                                        <input type="number" v-model="batchData.seckill_price"
                                                               placeholder="秒杀价格">
                                                    </div>
                                                    <div class="am-form-group">
                                                        <input type="number" min="0" v-model="batchData.seckill_stock"
                                                               placeholder="秒杀库存">
                                                    </div>
                                                    <div class="am-form-group">
                                                        <button @click="onSubmitBatchData" type="button"
                                                                class="am-btn am-btn-sm am-btn-secondaryam-radius">批量设置
                                                        </button>
                                                    </div>
                                                </div>
                                                <!-- sku table -->
                                                <table class="spec-sku-tabel am-table am-table-bordered am-table-centered
                                     am-margin-bottom-xs am-text-nowrap">
                                                    <tbody>
                                                    <tr>
                                                        <th v-for="item in spec_attr">{{ item.group_name }}</th>
                                                        <th>商家编码</th>
                                                        <th>商品售价</th>
                                                        <th>商品库存</th>
                                                        <th class="form-require">
                                                            秒杀价格
                                                        </th>
                                                        <th class="form-require">
                                                            秒杀库存
                                                        </th>
                                                    </tr>
                                                    <tr v-for="(item, index) in spec_list">
                                                        <td v-for="td in item.rows" class="td-spec-value am-text-middle"
                                                            :rowspan="td.rowspan">
                                                            {{ td.spec_value }}
                                                        </td>
                                                        <td>{{ item.form.goods_no ? item.form.goods_no : '--' }}</td>
                                                        <td>{{ item.form.goods_price }}</td>
                                                        <td>{{ item.form.stock_num }}</td>
                                                        <td>
                                                            <input type="number" min="0" class="ipt-w80"
                                                                   name="seckill_price"
                                                                   v-model="item.form.seckill_price" required>
                                                        </td>
                                                        <td>
                                                            <input type="number" min="0" class="ipt-w80"
                                                                   name="seckill_stock"
                                                                   v-model="item.form.seckill_stock" required>
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                                <div class="help-block">
                                                    <small>注：秒杀库存为独立库存，与主商品库存不同步</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="am-form-group am-padding-top">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">库存计算方式 </label>
                                <div class="am-u-sm-9 am-u-md-6 am-u-lg-5 am-u-end">
                                    <label class="am-radio-inline">
                                        <input type="radio" name="goods[deduct_stock_type]" value="10" data-am-ucheck
                                            <?= $model['deduct_stock_type'] == 10 ? 'checked' : '' ?> >
                                        下单减库存
                                    </label>
                                    <label class="am-radio-inline">
                                        <input type="radio" name="goods[deduct_stock_type]" value="20" data-am-ucheck
                                            <?= $model['deduct_stock_type'] == 20 ? 'checked' : '' ?> >
                                        付款减库存
                                    </label>
                                </div>
                            </div>

                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require"> 限购数量 </label>
                                <div class="am-u-sm-9 am-u-md-6 am-u-lg-5 am-u-end">
                                    <input type="number" min="0" class="tpl-form-input" name="goods[limit_num]"
                                           value="<?= $model['limit_num'] ?>" required>
                                    <small>注：每人限制购买的数量，如果填写0则不限购</small>
                                </div>
                            </div>
                            <div class="am-form-group am-padding-top">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require"> 商品状态 </label>
                                <div class="am-u-sm-9 am-u-md-6 am-u-lg-5 am-u-end">
                                    <label class="am-radio-inline">
                                        <input type="radio" name="goods[status]" value="1" data-am-ucheck
                                            <?= $model['status'] == 1 ? 'checked' : '' ?>>
                                        上架
                                    </label>
                                    <label class="am-radio-inline">
                                        <input type="radio" name="goods[status]" value="0" data-am-ucheck
                                            <?= $model['status'] == 0 ? 'checked' : '' ?>>
                                        下架
                                    </label>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">排序 </label>
                                <div class="am-u-sm-9 am-u-md-6 am-u-lg-5 am-u-end">
                                    <input type="number" min="0" class="tpl-form-input" name="goods[sort]"
                                           value="<?= $model['sort'] ?>" required>
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
        <input type="hidden" name="goods_id" value="{{ $value.goods_id }}">
    </div>
    {{ /each }}
</script>

<script src="assets/common/js/vue.min.js"></script>
<script src="assets/store/js/goods.spec.js?v=<?= $version ?>"></script>
<script>
    $(function () {

        // 注册商品多规格组件
        var specMany = new GoodsSpec({
            el: '#many-app',
            baseData: <?= \app\common\library\helper::jsonEncode($specData) ?>
        });

        /**
         * 表单验证提交
         * @type {*}
         */
        $('#my-form').superForm({
            // 获取多规格sku数据
            buildData: function () {
                var specData = specMany.appVue.getData();
                return {
                    goods: {
                        spec_many: {
                            spec_list: specData.spec_list
                        }
                    }
                };
            }
        });

    });
</script>
