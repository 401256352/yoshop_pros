<style>
    .result-index {
        padding: 48px 0;
        text-align: center;
    }

    .result-index-icon {
        color: #FF5F49;
        font-size: 72px;
    }

    .result-index-title {
        margin-bottom: 16px;
        color: #4a4a4a;
        font-weight: 500;
        font-size: 24px;
        line-height: 32px;
    }

    .result-index-description {
        margin-bottom: 24px;
        color: rgba(0, 0, 0, .55);
        font-size: 14px;
        line-height: 22px;
    }

    .result-index-action {
        margin-top: 32px;
    }
</style>
<div class="row-content am-cf">
    <div class="row">
        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <div class="widget am-cf">
                <div class="result-index">
                    <div class="result-item result-index-icon">
                        <i class="iconfont icon-unif060"></i>
                    </div>
                    <div class="result-item result-index-title">提交失败</div>
                    <div class="result-item result-index-description"><?= strip_tags($msg) ?></div>
                    <div class="result-item result-index-action">
                        <button type="submit" class="j-submit am-btn am-btn-sm am-btn-default">点击返回
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    (function () {
        var href = '<?= $url ?>';
        $('.j-submit').on('click', function () {
            location.href = href;
        });
    })();
</script>
