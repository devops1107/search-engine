<?php defined('SPARKIN') or die('xD'); ?>
<div class="container search-container py-4">
    <div class="row">
        <div class="col-md-8">
            <?php echo sp_alert_flashes('pages', true, false); ?>
            <div class="page-content">
                <?php echo $t['page.content_body']; ?>
            </div>

            <?php echo breadcrumb_render(); ?>
        </div>
    </div>
</div>

