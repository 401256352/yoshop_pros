<div class="row-content am-cf">
    <div class="row">
        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <div class="widget am-cf">
                <div class="widget-head am-cf">
                    <div class="widget-title am-cf"> 活动会场-场次列表</div>
                </div>
                <div class="widget-body am-fr">
                    <!-- 工具栏 -->
                    <div class="page_toolbar am-margin-bottom am-cf">
                        <form class="toolbar-form" action="">
                            <input type="hidden" name="s" value="/<?= $request->pathinfo() ?>">
                            <div class="am-u-sm-12 am-u-md-3">
                                <div class="am-form-group">
                                    <div class="am-btn-toolbar">
                                        <?php if (checkPrivilege('apps.sharp.active_time/add')): ?>
                                            <div class="am-btn-group am-btn-group-xs">
                                                <a class="am-btn am-btn-default am-btn-success am-radius"
                                                   href="<?= url('apps.sharp.active_time/add',
                                                       ['active_id' => $request->param('active_id')]) ?>">
                                                    <span class="am-icon-plus"></span> 新增场次
                                                </a>
                                            </div>
                                        <?php endif; ?>
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
                                <th>场次ID</th>
                                <th width="20%">场次时间</th>
                                <th>活动日期</th>
                                <th>商品数量</th>
                                <th>场次状态</th>
                                <th>创建时间</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (!$list->isEmpty()): foreach ($list as $item): ?>
                                <tr>
                                    <td class="am-text-middle"><?= $item['active_time_id'] ?></td>
                                    <td class="am-text-middle"><strong><?= $item['active_time'] ?></strong></td>
                                    <td class="am-text-middle"><?= $item['active']['active_date'] ?></td>
                                    <td class="am-text-middle">共<?= $item['goods_count'] ?>个</td>
                                    <td class="am-text-middle">
                                        <span class="j-state x-cur-p am-badge am-badge-<?= $item['status'] ? 'success' : 'warning' ?>"
                                              data-id="<?= $item['active_time_id'] ?>"
                                              data-state="<?= $item['status'] ?>">
                                               <?= $item['status'] ? '启用' : '禁用' ?></span>
                                    </td>
                                    <td class="am-text-middle"><?= $item['create_time'] ?></td>
                                    <td class="am-text-middle">
                                        <div class="tpl-table-black-operation">
                                            <?php if (checkPrivilege('apps.sharp.active_time/edit')): ?>
                                                <a href="<?= url('apps.sharp.active_time/edit', ['id' => $item['active_time_id']]) ?>"
                                                   class="tpl-table-black-operation-default">
                                                    <i class="am-icon-pencil"></i> 编辑
                                                </a>
                                            <?php endif; ?>
                                            <?php if (checkPrivilege('apps.sharp.active_time/delete')): ?>
                                                <a href="javascript:void(0);"
                                                   class="item-delete tpl-table-black-operation-default"
                                                   data-id="<?= $item['active_time_id'] ?>">
                                                    <i class="am-icon-trash"></i> 删除
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; else: ?>
                                <tr>
                                    <td colspan="7" class="am-text-center">暂无记录</td>
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

        // 活动状态
        $('.j-state').click(function () {
            // 验证权限
            if (!"<?= checkPrivilege('apps.sharp.active_time/state')?>") {
                return false;
            }
            var data = $(this).data();
            layer.confirm('确定要' + (parseInt(data.state) ? '禁用' : '启用') + '该活动吗？'
                , {title: '友情提示'}
                , function (index) {
                    $.post("<?= url('apps.sharp.active_time/state') ?>", {
                        id: data.id,
                        state: Number(!(parseInt(data.state)))
                    }, function (result) {
                        result.code === 1 ? $.show_success(result.msg, result.url)
                            : $.show_error(result.msg);
                    });
                    layer.close(index);
                });
        });

        // 删除元素
        var url = "<?= url('apps.sharp.active_time/delete') ?>";
        $('.item-delete').delete('id', url, '确定要删除吗？删除后不可恢复，请谨慎操作');

    });
</script>

