<div class="row-content am-cf">
    <div class="row">
        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <div class="widget am-cf">
                <form id="my-form" class="am-form tpl-form-line-form" method="post">
                    <div class="widget-body">
                        <fieldset>
                            <div class="widget-head am-cf">
                                <div class="widget-title am-fl"> 砍价设置</div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require"> 是否开启分销 </label>
                                <div class="am-u-sm-9 am-u-md-6 am-u-lg-5 am-u-end">
                                    <label class="am-radio-inline">
                                        <input type="radio" name="basic[is_dealer]" value="1"
                                            <?= $values['is_dealer'] == 1 ? 'checked' : '' ?>
                                               data-am-ucheck required>
                                        开启
                                    </label>
                                    <label class="am-radio-inline">
                                        <input type="radio" name="basic[is_dealer]" value="0"
                                               data-am-ucheck <?= $values['is_dealer'] == 0 ? 'checked' : '' ?>>
                                        关闭
                                    </label>
                                    <div class="help-block">
                                        <small>注：如需使用分销功能必须在 [分销中心 - 分销设置] 中开启</small>
                                    </div>
                                </div>
                            </div>
                            <div class="am-form-group am-padding-top">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require"> 砍价规则 </label>
                                <div class="am-u-sm-9 am-u-md-6 am-u-lg-5 am-u-end">
                                    <textarea class="am-field-valid" rows="8" placeholder="请输入砍价规则"
                                              name="basic[bargain_rules]"><?= $values['bargain_rules'] ?></textarea>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <div class="am-u-sm-9 am-u-sm-push-3 am-margin-top-lg">
                                    <button type="submit" class="j-submit am-btn am-btn-sm am-btn-secondary">提交
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
<script>
    $(function () {

        /**
         * 表单验证提交
         * @type {*}
         */
        $('#my-form').superForm();

    });
</script>
