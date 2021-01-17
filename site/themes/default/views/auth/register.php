<?php
/**
 * Register Page Template
 *
 * This file contains the login page layout
 */
defined('SPARKIN') or die('xD');
?>
<div class="row no-gutters auth-wrap">
    <div class="col-sm-5 d-flex justify-content-center flex-column h-100">
        <div class="container auth-form-container">
            <form
            class="card shadow-none"
            method="POST"
            action="<?= e_attr(url_for('auth.register_post')); ?><?php echo $t['redirect_to_query']; ?>"
            data-ajax-form="true"
            data-recaptcha-id="auth-captcha"
            data-parsley-validate
            >
            <?php echo $t['csrf_html'] . $t['honeypot_html']; ?>

            <div class="card-body">

             <div class="auth-logo-wrap">
                <a href="<?php echo e_attr(url_for('site.home')); ?>" class="sp-link auth-logo-link">
                    <img src="<?php echo e_attr($t['auth_logo_url']); ?>" class="auth-logo" alt="<?php echo e_attr(get_option('site_name')); ?>">
                </a>
            </div>

            <h3 class="auth-heading"><?php echo __('register-heading', _T); ?></h3>
            <h6 class="auth-subheading"><?php echo __('register-heading-subtitle', _T); ?></h6>


            <?php if (has_items($t['providers'])) : ?>
                <div class="providers py-2 my-4">
                    <?php insert('auth/partials/social_login_buttons.php'); ?>
                </div>

            <?php endif; ?>


            <?php echo sp_alert_flashes('account', true, false); ?>

            <div class="form-group">
                <div class="floating-label textfield-box">
                    <label class="form-label" for="email"><?php echo __('email', _T); ?></label>
                    <input
                    type="email" class="form-control" id="email" name="email"
                    value="<?php echo sp_post('email'); ?>"
                    placeholder="<?php echo e_attr(__('email-placeholder', _T)); ?>"
                    data-parsley-remote="<?php echo e_attr(url_for('ajax.email_check')); ?>"
                    data-parsley-remote-reverse="true"
                    data-parsley-remote-message="<?php echo e_attr(__('email-exists', _T)); ?>"
                    autocomplete="disabled"
                    required>
                </div>
            </div>

            <div class="form-group">
                <div class="floating-label textfield-box">
                  <label class="form-label" for="password"><?php echo __('password', _T); ?></label>

                  <input
                  autocomplete="false"
                  type="password" name="password" id="password" class="form-control"
                  placeholder="<?php echo __("password-placeholder-register", _T); ?>"
                  minlength="<?php echo e_attr(config('internal.password_minlength')); ?>" required>
              </div>
          </div>


          <div class="form-group">
            <div class="floating-label textfield-box">
                <label class="form-label" for="full_name"><?php echo __('full-name', _T); ?></label>

                <input
                type="text" name="full_name" id="full_name" class="form-control"
                placeholder="<?php echo __("full-name-placeholder", _T); ?>"
                value="<?php echo sp_post('full_name'); ?>"
                minlength="3" maxlength="200" required>
            </div>
        </div>


        <div class="form-group">
                <div class="floating-label textfield-box">
                    <label class="form-label" for="gender"><?php echo __('gender', _T); ?></label>

                    <select class="custom-select" name="gender" id="gender">
                        <?php foreach (sp_genders(_T) as $_gender_id => $_gender_label) :?>
                          <option value="<?php echo e_attr($_gender_id); ?>" <?php echo sp_post('gender') == $_gender_id ? 'selected' : ''; ?>><?php echo e($_gender_label); ?></option>
                        <?php endforeach; ?>
                  </select>

              </div>
    </div>

        <?php echo sp_google_recaptcha('auth.register', '<div class="recaptcha-container">', '</div>', false, [], 'auth-captcha');?>

        <div class="form-group text-right">
            <button type="submit" class="btn btn-primary">
                <span class="btn-text"><?php echo __("register-btn-text", _T); ?></span>
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            </button>
        </div>



           <p class="d-sm-none py-3"><?php echo __("already-have-an-account", _T); ?>
            <a href="<?php echo e_attr(url_for('auth.signin') . $t['redirect_to_query']);  ?>" class="sp-link"><?php echo __("sign-in-now", _T); ?></a></p>
</div>
</div>
</form>
<!-- ./card -->
</div>

<div class="col-sm-7 auth-sidebar-right">
       <h3 class="auth-heading"><?php echo __("already-have-an-account", _T); ?></h3>
       <h6 class="auth-subheading"><?php echo __("already-have-an-account-subtitle", _T); ?></h6>
       <a href="<?php echo e_attr(url_for('auth.signin') . $t['redirect_to_query']);  ?>" class="sp-link btn btn-outline-dark btn-lg"><?php echo __("sign-in-now", _T); ?></a>
</div>
</div>
<!-- ./container -->
