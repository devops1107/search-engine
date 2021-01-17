<?php block('content'); ?>
<div class="row">
  <div class="container">
    <?php echo sp_alert_flashes('menus'); ?>
    <form method="post" action="?" class="card" data-parsley-validate>
        <?php echo $t['csrf_html']?>
      <div class="card-body">
        
            <div class="form-group">
            <label class="form-label" for="menu_name"><?= __('Menu Name'); ?></label>
            <input type="text" name="menu_name" id="menu_name" class="form-control" value="<?= sp_post('menu_name'); ?>" maxlength="200" required>
            <div class="form-text text-muted">
                <?= __('The menu name'); ?>
            </div>
            </div>
      </div>
      <div class="card-footer text-right">
        <button type="submit" class="btn btn-secondary ml-auto"><?php echo __('Create')?></button>
      </div>
    </form>
  </div>
</div>
<?php endblock(); ?>
<?php
extend(
    'admin::layouts/skeleton.php',
    [
      'title' => __('Create Menu'),
      'body_class' => 'menus menus-create',
      'page_heading' => __('Create Menu'),
      'page_subheading' => __('Add a new menu.'),
    ]
);
