<?php block('content'); ?>
<div class="row">
  <div class="container">
    <?php echo sp_alert_flashes('roles'); ?>
    <form method="post" action="?" class="card" data-parsley-validate>
        <?php echo $t['csrf_html']?>
      <div class="card-body">
        <div class="form-group">
          <label class="form-label" for="role_name"><?php echo __('Role Name'); ?></label>
          <input type="text" id="role_name" name="role_name" value="<?php echo e_attr($t['role.role_name']); ?>" minlength="3" maxlength="200" class="form-control" required>
        </div>
        <div class="form-group">
          <label class="form-label"><?php echo __('Permissions'); ?></label>
        <?php if ((int) $t['role.is_protected']) : ?>
            <?php echo
            sp_bootstrap_alert(
                __('Modifying system role\'s permissions may result in unexpected behaviours.'),
                'secondary small',
                sp_svg_icon_for_alert('info'),
                false
            );
            ?>
        <?php endif;?>
          <div class="row">
            <?php foreach ($t['role.permissions'] as $permID => $permItem) : ?>
              <div class="col-md-6">
                <label class="custom-control custom-checkbox custom-control-inline">
                  <input type="checkbox" name="permissions[]" class="custom-control-input" value="<?php echo e_attr($permID); ?>" <?php checked($permItem['state']); ?>>
                  <span class="custom-control-label"><?php echo e($permItem['label']); ?></span>
                </label>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
</div>
<div class="card-footer text-right">
        <button type="submit" class="btn btn-secondary ml-auto"><?php echo __('Update')?></button>
      </div>
    </form>

  </div>
</div>
<?php endblock(); ?>
<?php
extend(
    'admin::layouts/skeleton.php',
    [
    'title' => __('Update Role'),
    'body_class' => 'roles roles-update',
    'page_heading' => __('Update Role'),
    'page_subheading' => __('Modify existing role.'),
    'page_heading_classes' => 'container'
    ]
);
