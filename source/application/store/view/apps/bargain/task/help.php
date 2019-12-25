<div class="row-content am-cf">
    <div class="row">
        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <div class="widget am-cf">
                <div class="widget-head am-cf">
                    <div class="widget-title am-cf"> 砍价助力榜</div>
                </div>
                <div class="widget-body am-fr">
                    <div class="am-scrollable-horizontal am-u-sm-12">
                        <table width="100%" class="am-table am-table-compact am-table-striped
                         tpl-table-black am-text-nowrap">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>用户头像</th>
                                <th>用户昵称</th>
                                <th>砍掉的金额</th>
                                <th>操作时间</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (!$list->isEmpty()): foreach ($list as $item): ?>
                                <tr>
                                    <td class="am-text-middle"><?= $item['help_id'] ?></td>
                                    <td class="am-text-middle">
                                        <img src="<?= $item['user']['avatarUrl'] ?>" width="72" alt="">
                                    </td>
                                    <td class="am-text-middle">
                                        <span><?= $item['user']['nickName'] ?></span>
                                        <?php if ($item['is_creater']): ?>
                                            <span class="am-badge am-badge-success">发起人</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="am-text-middle">-<?= $item['cut_money'] ?></td>
                                    <td class="am-text-middle"><?= $item['create_time'] ?></td>
                                </tr>
                            <?php endforeach; else: ?>
                                <tr>
                                    <td colspan="5" class="am-text-center">暂无记录</td>
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

