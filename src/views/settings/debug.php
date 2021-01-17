<?php breadcrumb_add('dashboard.settings.debug', __('Debugging')); ?>

<?php block('form-content'); ?>
<!--<div class="form-group">
  <label class="form-label"><?php echo __('Cron Job Command'); ?></label>
  <div class="form-control-plaintext">
    <?php if (is_demo()) : ?>
      <code><?php echo __('command hidden in demo mode'); ?></code>
    <?php else : ?>
    <code>wget -q -O /dev/null "<?php echo e_attr(url_for('tasks')); ?>?token=<strong><?php echo e_attr(get_option('spark_cron_job_token')); ?></strong>"</code>
    <?php endif; ?>
  </div>

  <span class="form-text text-muted"><?php echo __('Set up this cron task to run every 5/10 minutes depending your server resources.'); ?></span>
</div>
<div class="form-group">
  <label class="form-label"><?php echo __('Cron Job URL (alternative)'); ?></label>
  <div class="form-control-plaintext">
    <?php if (is_demo()) : ?>
      <code><?php echo __('URL hidden in demo mode'); ?></code>
    <?php else : ?>
    <code><?php echo e_attr(url_for('tasks')); ?>?token=<strong><?php echo e_attr(get_option('spark_cron_job_token')); ?></strong></code>
    <?php endif; ?>
  </div>

  <span class="form-text text-muted"><?php echo __('If you use web based cron services, just setup that service to ping this URL. You only need to set one of the above.'); ?></span>
</div>

<div class="form-group">
  <label class="form-label" for="regen_cron_token"><?php echo __('Re-generate Cron Job Token'); ?></label>
  <label class="custom-switch mt-3">
    <input type="hidden" name="regen_cron_token" value="0">
    <input type="checkbox" name="regen_cron_token" value="1" class="custom-switch-input">
    <span class="custom-switch-indicator"></span>
    <span class="custom-switch-description"> <?php echo __('Re-generate the cron token'); ?></span>
  </label>
  <span class="form-text text-muted"><?php echo __('Regenerate the cron job token for security purpose. <span class="d-block text-danger">If you regenerate the token you will need to update your cron job from cPanel again.</span>'); ?></span>
</div>-->

<div class="form-group">
  <label class="form-label" for="flush_cache"><?php echo __('Flush Cache'); ?></label>
  <label class="custom-switch mt-3">
    <input type="hidden" name="flush_cache" value="0">
    <input type="checkbox" name="flush_cache" value="1" class="custom-switch-input">
    <span class="custom-switch-indicator"></span>
    <span class="custom-switch-description"> <?php echo __('Clear everything from the cache pool'); ?></span>
  </label>
  <span class="form-text text-muted"><?php echo __('Completely flush the cache pool including generated thumbnails.'); ?></span>
</div>
<div class="form-group">
  <label class="form-label" for="clear_attempts"><?php echo __('Clear Attempt Table'); ?></label>
  <label class="custom-switch mt-3">
    <input type="hidden" name="clear_attempts" value="0">
    <input type="checkbox" name="clear_attempts" value="1" class="custom-switch-input">
    <span class="custom-switch-indicator"></span>
    <span class="custom-switch-description"> <?php echo __('Clear User Attempts'); ?></span>
  </label>
  <span class="form-text text-muted"><?php echo __('Completely flush the user attempt table'); ?></span>
</div>
<div class="form-group">
  <label class="form-label" for="clear_tokens"><?php echo __('Clear Expired Tokens'); ?></label>
  <label class="custom-switch mt-3">
    <input type="hidden" name="clear_tokens" value="0">
    <input type="checkbox" name="clear_tokens" value="1" class="custom-switch-input">
    <span class="custom-switch-indicator"></span>
    <span class="custom-switch-description"> <?php echo __('Clear Tokens'); ?></span>
  </label>
  <span class="form-text text-muted"><?php echo __('Remove expired user tokens from database'); ?></span>
</div>
<div class="form-group">
  <label class="form-label" for="clear_logs"><?php echo __('Clear Log Files'); ?></label>
  <label class="custom-switch mt-3">
    <input type="hidden" name="clear_logs" value="0">
    <input type="checkbox" name="clear_logs" value="1" class="custom-switch-input">
    <span class="custom-switch-indicator"></span>
    <span class="custom-switch-description"> <?php echo __('Clear Logs'); ?></span>
  </label>
  <span class="form-text text-muted"><?php echo __('Delete log files'); ?></span>
</div>
<?php endblock(); ?>
<?php

// Extends the base skeleton
extend(
    'admin::layouts/settings_skeleton.php',
    [
    'title' => __('Debugging'),
    'body_class' => 'settings debug-settings',
    'page_heading' => __('Debugging'),
    'page_subheading' => __("Debug and develop"),
    'form_btn_label' => __('Run Actions')
    ]
);
