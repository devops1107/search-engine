<?php breadcrumb_add('dashboard.settings.services', __('Services Settings')); ?>

<?php block('form-content'); ?>
<div class="form-group">
  <label class="form-label" for="captcha_enabled"><?php echo __('Google Recaptcha v2'); ?></label>
  <label class="custom-switch mt-3">
    <input type="hidden" name="captcha_enabled" value="0">
    <input type="checkbox" id="captcha_enabled" name="captcha_enabled" value="1" class="custom-switch-input" data-toggle-prefix=".google-captcha-settings-" <?php checked(1, (int) sp_post('captcha_enabled', get_option('captcha_enabled'))); ?>>
    <span class="custom-switch-indicator"></span>
    <span class="custom-switch-description"> <?php echo __('Enable Google Recaptcha v2'); ?></span>
  </label>
  <span class="form-text text-muted"><?php echo __('Toggle site-wide Google Recaptcha v2 verification'); ?></span>
</div>

<section class="google-captcha-settings-1">
  <div class="form-group">
    <label for="google_recaptcha_secret_key" class="form-label"><?php echo __('Google Recaptcha v2 Secret Key'); ?></label>
    <input type="text" class="form-control captcha-input" name="google_recaptcha_secret_key" id="google_recaptcha_secret_key" value="<?php echo sp_post('google_recaptcha_secret_key', get_option('google_recaptcha_secret_key')); ?>" <?php echo (int) get_option('captcha_enabled') ? 'required' : ''; ?>>
    <span class="form-text text-muted"><?php echo __('Required for Google Recaptchas to work'); ?></span>
  </div>
  <div class="form-group">
    <label for="google_recaptcha_site_key" class="form-label"><?php echo __('Google Recaptcha v2 Site Key'); ?></label>
    <input type="text" class="form-control captcha-input" name="google_recaptcha_site_key" id="google_recaptcha_site_key" value="<?php echo sp_post('google_recaptcha_site_key', get_option('google_recaptcha_site_key')); ?>" <?php echo (int) get_option('captcha_enabled') ? 'required' : ''; ?>>
    <span class="form-text text-muted"><?php echo __('Required for Google Recaptchas to work'); ?></span>
  </div>

  <div class="form-group">
      <label class="form-label mb-3"><?php echo __('Hide Recaptcha in:'); ?></label>
      <div class="custom-controls-stacked">
        <?php foreach ($t['captcha_locations'] as $key => $label) : ?>
          <label class="custom-control custom-checkbox custom-control-inline">
            <input type="checkbox" class="custom-control-input" name="ignore_captcha_locations[<?php echo e_attr($key); ?>]" value="1" <?php echo !empty($t['checked_locations'][$key]) ? 'checked=""' : '' ; ?>>
            <span class="custom-control-label"><?php echo e($label); ?></span>
        </label>
        <?php endforeach; ?>
    <span class="form-text text-muted">
        <?php echo __('By default captchas would be shown in any necessary form when enabled. But you can choose not to show captchas in some place by marking them here.'); ?>
    </span>
</div>
  </div>

</section>

<div class="form-group">
  <label class="form-label" for="facebook_app_id"><?php echo __('Facebook App Id'); ?></label>
  <input type="number" class="form-control" name="facebook_app_id" id="facebook_app_id" value="<?php echo sp_post('facebook_app_id', get_option('facebook_app_id')); ?>" maxlength="200" min="0">
  <p class="form-text text-muted">
    <?php echo __('Facebook APP Id for this app, consider filling it as some part of the site may need it'); ?>
  </p>
</div>
<?php endblock(); ?>

<?php block('body_end'); ?>
<script type="text/javascript">
  $(function () {
    $(document).on('change', '#captcha_enabled', function(e) {
        var checked = $(this).is(':checked');

        if (checked) {
            $('.captcha-input').attr('required', true);
        } else {
            $('.captcha-input').attr('required', false);
        }
    });

    $(document).on('change', '#onesignal_enabled', function(e) {
        var checked2 = $(this).is(':checked');

        if (checked2) {
            $('.onesignal-input').attr('required', true);
        } else {
            $('.onesignal-input').attr('required', false);
        }
    });

});
</script>
<?php endblock(); ?>
<?php

// Extends the base skeleton
extend(
    'admin::layouts/settings_skeleton.php',
    [
    'title' => __('Services Settings'),
    'body_class' => 'settings services-settings',
    'page_heading' => __('Services Settings'),
    'page_subheading' => __("Manage third party services"),
    ]
);
