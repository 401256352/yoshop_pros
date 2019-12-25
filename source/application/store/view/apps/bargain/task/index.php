<div class="row-content am-cf">
    <div class="row">
        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <div class="widget am-cf">
                <div class="widget-head am-cf">
                    <div class="widget-title am-cf"> 砍价记录</div>
                </div>
                <div class="widget-body am-fr">
                    <!-- 工具栏 -->
                    <div class="page_toolbar am-margin-bottom am-cf">
                        <form class="toolbar-form" action="">
                            <input type="hidden" name="s" value="/<?= $request->pathinfo() ?>">
                            <div class="am-u-sm-12 am-u-md-9 am-u-sm-push-3">
                                <div class="am fr">
                                    <div class="am-form-group am-fl">
                                        <div class="am-input-group am-input-group-sm tpl-form-border-form">
                                            <input type="text" class="am-form-field" name="search"
                                                   placeholder="请输入商品名称/用户昵称"
                                                   value="<?= $request->get('search') ?>">
                                            <div class="am-input-group-btn">
                                                <button class="am-btn am-btn-default am-icon-search"
                                                        type="submit"></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="am-scrollable-horizontal am-u-sm-12">
                        <table width="100%" class="am-table am-table-compact am-table-striped
                         tpl-table-black am-text-nowrap">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th width="10%">商品信息</th>
                                <th>用户信息</th>
                                <th>砍价底价</th>
                                <th>已砍金额</th>
                                <th>截止时间</th>
                                <th>是否购买</th>
                                <th>砍价状态</th>
                                <th>创建时间</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (!$list->isEmpty()): foreach ($list as $item): ?>
                                <tr>
                                    <td class="am-text-middle"><?= $item['task_id'] ?></td>
                                    <td class="am-text-middle">
                                        <div class="goods-detail">
                                            <div class="goods-image">
                                                <img src="<?= $item['goods']['goods_image'] ?>" alt="">
                                            </div>
                                            <div class="goods-info dis-flex flex-dir-column flex-x-center">
                                                <p class="goods-title"><?= $item['goods']['goods_name'] ?></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="am-text-middle">
                                        <div class="user-detail am-cf">
                                            <div class="user-avatar">
                                                <img src="<?= $item['user']['avatarUrl'] ?>" alt="">
                                            </div>
                                            <div class="detail-info dis-flex flex-dir-column flex-x-center">
                                                <p class="detail-info_item"><?= $item['user']['nickName'] ?></p>
                                                <p class="detail-info_item">(ID：<?= $item['user']['user_id'] ?>)</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="am-text-middle"><?= $item['floor_price'] ?></td>
                                    <td class="am-text-middle"><?= $item['cut_money'] ?></td>
                                    <td class="am-text-middle"><?= $item['end_time'] ?></td>
                                    <td class="am-text-middle">
                                        <span class="am-badge am-badge-<?= $item['is_buy'] ? 'success' : '' ?>">
                                               <?= $item['is_buy'] ? '已购买' : '未购买' ?></span>
                                    </td>
                                    <td class="am-text-middle">
                                        <span class="am-badge am-badge-<?= $item['status'] ? 'secondary' : 'warning' ?>">
                                               <?= $item['status'] ? '砍价中' : '已结束' ?></span>
                                    </td>
                                    <td class="am-text-middle"><?= $item['create_time'] ?></td>
                                    <td class="am-text-middle">
                                        <div class="tpl-table-black-operation">
                                            <?php if (checkPrivilege('apps.bargain.active/help')): ?>
                                                <a href="<?= url('apps.bargain.task/help', ['task_id' => $item['task_id']]) ?>"
                                                   class="tpl-table-black-operation-default">
                                                    查看砍价榜
                                                </a>
                                            <?php endif; ?>
                                            <?php if (checkPrivilege('apps.bargain.active/delete')): ?>
                                                <a href="javascript:void(0);"
                                                   class="item-delete tpl-table-black-operation-default"
                                                   data-id="<?= $item['task_id'] ?>">
                                                    <i class="am-icon-trash"></i> 删除
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; else: ?>
                                <tr>
                                    <td colspan="10" class="am-text-center">暂无记录</td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="am-u-lg-12 am-cf">
                        <div class="am-fr"><?= $list->render() ?> </div>
                        <div class="am-fr pagination-total am-margin-right">
                            <div class="am-vertical-align-middle">总记录：<?= $list->total() ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(function () {

        // 删除元素
        var url = "<?= url('apps.bargain.task/delete') ?>";
        $('.item-delete').delete('task_id', url);

    });
</script>

