<?php block('content'); ?>
<div class="row"><div class="container">
    <?= sp_alert_flashes('account'); ?>
    <form method="post" action="?" class="card" enctype="multipart/form-data" data-parsley-validate>
        <?=$t['csrf_html']?>
      <div class="card-body form-row">
        <div class="form-group col-md-6">
          <label class="form-label" for="email"><?= __('E-Mail'); ?></label>
          <input type="email" name="email" id="email" value="<?= sp_post('email', $t['user.email']); ?>"
          class="form-control" maxlength="200"
          data-parsley-remote="<?= e_attr(url_for('ajax.email_check')); ?>?email={value}&except=<?=e_attr($t['user.email']);?>"
          data-parsley-remote-reverse="true"
          placeholder="<?= e_attr(__('tony@stark-industries.com')); ?>"
          data-parsley-remote-message="<?= e_attr(__('E-Mail already exists in database.')); ?>"
          required>
        </div>
        <div class="form-group col-md-6">
          <label class="form-label" for="full_name"><?= __('Full Name'); ?></label>
          <input type="text" name="full_name" id="full_name" value="<?= sp_post('full_name', $t['user.full_name']); ?>"
          placeholder="<?= __('Tony Stark'); ?>" class="form-control"
          maxlength="200">
        </div>
        <div class="form-group col-md-6">
          <label class="form-label" for="old_password"><?= __('Current Password'); ?> <a href="<?= e_attr(url_for('auth.forgotpass')); ?>" class="float-right small"><?= __("Forgot Password"); ?></a></label>
          <input type="password" name="old_password" id="old_password"
          class="form-control" value="<?= sp_post('old_password'); ?>"
          data-parsley-required-message="<?= e_attr(__('You must provide your current password in order to change your password')); ?>">
          <span class="form-text text-muted small"><?= __("Needed if you wan't to change your password."); ?></span>
        </div>

        <div class="form-group col-md-6">
          <label class="form-label" for="password"><?= __('New Password'); ?></label>
          <input type="password" name="password" id="password" class="form-control" value="<?= sp_post('password'); ?>"
          placeholder="<?= e_attr(sprintf("%d characters or more", config('internal.password_minlength'))); ?>"
          minlength="<?= e_attr(config('internal.password_minlength')); ?>">
          <span class="form-text text-muted small"><?= __("Leave blank if you don't want to change."); ?></span>
        </div>
        <div class="form-group col-6">
          <label class="form-label" for="gender"><?= __('Gender'); ?></label>

          <div class="custom-controls-stacked">
            <?php foreach (sp_genders() as $_gender_id => $_gender_label) :?>
              <label class="custom-control custom-radio custom-control-inline">
                <input type="radio" class="custom-control-input" name="gender" value="<?= e_attr($_gender_id); ?>" <?= (int) sp_post('gender', $t['user.gender']) == $_gender_id ? 'checked' : ''; ?>>
                <span class="custom-control-label"><?= e($_gender_label); ?></span>
              </label>
            <?php endforeach; ?>
          </div>
        </div>

        <div class="form-group col-md-6">
            <div class="form-label"><?= __("Avatar"); ?></div>
            <div class="custom-file">
              <input type="file" class="custom-file-input" name="avatar" accept="image/*">
              <label class="custom-file-label"><?=__("Choose image"); ?></label>
          </div>
      </div>

      <div class="form-group col-md-6">
        <label class="form-label" for="force_gravatar"><?= __('Force Gravatar'); ?></label>
        <label class="custom-switch mt-3">
          <input type="hidden" name="force_gravatar" value="0">
          <input type="checkbox" id="force_gravatar" name="force_gravatar" value="1" class="custom-switch-input" <?php checked(1, (int) sp_post('force_gravatar', get_option('force_gravatar'))); ?>>
          <span class="custom-switch-indicator"></span>
          <span class="custom-switch-description"> <?= __('Remove avatar and use Gravatar'); ?></span>
        </label>
      </div>

      </div>
      <div class="card-footer text-right">
        <button type="submit" class="btn btn-primary ml-auto"><?=__('Update')?></button>
      </div>
    </form>
  </div>
</div>
<?php endblock(); ?>

<?php block('body_end'); ?>
<script type="text/javascript">
  $(function () {
    /**
     * Mark the password confirm field as required
     */
    $('#password').on('change', function (e) {
      var password = $(this).val();
      var old_password = $("#old_password");

      if (password.length > 0) {
        old_password.focus().prop('required', true);
        //old_password.parsley().validate();
      } else {
        old_password.prop('required', false);
        old_password.parsley().reset();
      }

    });
  });
</script>
<?php endblock(); ?>
<?php

// Extends the base skeleton
extend(
    'admin::layouts/skeleton.php',
    [
        'title' => __('Account Settings'),
        'body_class' => 'account-settings',
        'page_heading' => __('Account Settings'),
        'page_subheading' => __('Update your account.'),
    ]
);
