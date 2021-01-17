<?php breadcrumb_add('dashboard.settings.site', __('API Settings')); ?>

<?php block('form-content'); ?>
<div class="form-group">
  <label for="server_api_key" class="form-label"><?php echo __('Server API Key'); ?></label>
  <input id="server_api_key" type="text" value="<?php echo is_demo() ? 'hidden in demo mode' : e_attr(get_option('server_api_key')); ?>" class="form-control" readonly>
</div>
<div class="form-group">
  <label class="form-label" for="regen_server_key"><?php echo __('Re-generate API Key'); ?></label>
  <label class="custom-switch mt-3">
    <input type="hidden" name="regen_server_key" value="0">
    <input type="checkbox" name="regen_server_key" value="1" class="custom-switch-input">
    <span class="custom-switch-indicator"></span>
    <span class="custom-switch-description"> <?php echo __('Re-generate the API Key'); ?></span>
  </label>
  <span class="form-text text-muted"><?php echo __('Regenerate the API key for security purpose. <span class="d-block text-danger">If you regenerate the API, all the existing apps that use this key will stop working.</span>'); ?></span>
</div>
<?php endblock(); ?>
<?php

// Extends the base skeleton
extend(
    'admin::layouts/settings_skeleton.php',
    [
    'title' => __('API Settings'),
    'body_class' => 'settings api-settings',
    'page_heading' => __('API Settings'),
    'page_subheading' => __("Manage API access"),
    ]
);
