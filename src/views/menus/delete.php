<?php block('content'); ?>
<div class="row">
  <div class="col-md-10">
    <?php echo sp_alert_flashes('menus'); ?>
    <form method="post" action="?" class="card">
        <?php echo $t['csrf_html']; ?>
        <div class="card-header">
            <h3 class="card-title"><?php echo __("Confirm Deletion"); ?></h3>
        </div>
        <div class="card-body">
            <div class="form-group">
                <span class="form-text">
                    <?php echo __("Are you sure you want to delete this menu?"); ?>
                    </span>
                </div>
                <div class="form-group">
                    <a href="<?php echo e_attr(url_for('dashboard.menus')); ?>" class="btn btn-outline-primary mr-1">
                        <?php echo __('Nope'); ?></a>
                    <button type="submit" class="btn btn-danger"><?php echo __('Confirm'); ?></button>
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
      'title' => __('Delete Menu'),
      'body_class' => 'menus menus-delete',
      'page_heading' => __('Delete Menu'),
      'page_subheading' => __('Remove existing menu.'),
    ]
);
