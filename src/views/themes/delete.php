<?php block('content'); ?>
<div class="row">
  <div class="col-md-10">
    <?php echo sp_alert_flashes('themes'); ?>
    <form method="post" action="?" class="card">
        <?php echo $t['csrf_html']?>
        <div class="card-header">
            <h3 class="card-title"><?php echo __("Confirm Deletion"); ?></h3>
        </div>
        <div class="card-body">
            <div class="form-group">
                <div class="bg-light p-3 rounded">
                      <span class="d-block mb-1"><?php echo e($t['meta.name']); ?></span>
                      <span class="text-muted small">v<?php echo e($t['meta.version']); ?> |
                      <?php echo sprintf(__('By <a href="%1$s">%2$s</a>'), e_attr($t['meta.author_uri']), e_attr($t['meta.author'])); ?>  |
                      <a href="<?php echo e_attr($t['meta.uri']); ?>"><?php echo __('Details'); ?></a></span>
                  </div>

                <span class="form-text">
                    <?php echo __("Are you sure you want to delete this theme?"); ?>
                    </span>
                </div>
                <div class="form-group">
                    <a href="<?php echo e_attr(url_for('dashboard.themes')); ?>" class="btn btn-outline-primary mr-1">
                        <?php echo __('Nope')?></a>
                    <button type="submit" class="btn btn-danger"><?php echo __('Confirm')?></button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php endblock(); ?>
<?php
extend(
    'admin::layouts/skeleton.php',
    [
      'title' => __('Delete Theme'),
      'body_class' => 'themes themes-create',
      'page_heading' => __('Delete Theme'),
      'page_subheading' => __('Remove existing theme.'),
    ]
);
