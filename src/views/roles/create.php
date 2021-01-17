<?php block('content'); ?>
<div class="row">
  <div class="container">
    <?php echo sp_alert_flashes('roles'); ?>
    <form method="post" action="?" class="card" data-parsley-validate>
        <?php echo $t['csrf_html']?>
      <div class="card-body">
        <div class="form-group">
          <label class="form-label" for="role_name"><?php echo __('Role Name'); ?></label>
          <input type="text" id="role_name" name="role_name" value="<?php echo sp_post('role_name'); ?>" minlength="3" maxlength="200" class="form-control" data-parsley-type="alphanum" required>
        </div>
        <div class="form-group">
          <label class="form-label"><?php echo __('Permissions'); ?></label>
          <div class="row">
            <?php foreach ($t['permissions'] as $permID => $label) : ?>
              <div class="col-md-6">
                <label class="custom-control custom-checkbox custom-control-inline">
                  <input type="checkbox" name="permissions[]" class="custom-control-input" value="<?php echo e_attr($permID); ?>">
                  <span class="custom-control-label"><?php echo e($label); ?></span>
                </label>
              </div>
            <?php endforeach; ?>
          </div>
        </div>

</div>
<div class="card-footer text-right">
        <button type="submit" class="btn btn-secondary ml-auto" id="form-submit"><?php echo __('Create')?></button>
      </div>
    </form>

  </div>
</div>

<?php endblock(); ?>
<?php
extend(
    'admin::layouts/skeleton.php',
    [
      'title' => __('Create Role'),
      'body_class' => 'roles role-create',
      'page_heading' => __('Create Role'),
      'page_subheading' => __('Add a new role.'),
      'page_heading_classes' => 'container'
    ]
);
