<?php
/**
 * Sign In Page Template
 *
 * This file contains the login page layout
 */
defined('SPARKIN') or die('xD');
?>

<div class="row no-gutters auth-wrap">
    <div class="auth-sidebar-left col-sm-5 d-flex justify-content-center flex-column h-100">

        <div class="container auth-form-container">
            <form
            class="card bg-transparent shadow-none"
            method="POST"
            action="<?= e_attr(url_for('auth.signin_post')); ?><?php echo $t['query_string']; ?>"
            data-recaptcha-id="auth-captcha"
            data-ajax-form="true"
            data-parsley-validate>
            <?php echo $t['csrf_html'] . $t['honeypot_html']; ?>

            <div class="card-body">
             <div class="auth-logo-wrap">
                <a href="<?php echo e_attr(url_for('site.home')); ?>" class="auth-logo-link sp-link">
                   <img src="<?php echo e_attr($t['auth_logo_url']); ?>" class="auth-logo" alt="<?php echo e_attr(get_option('site_name')); ?>">
                </a>
            </div>

            <h3 class="auth-heading"><?php echo __('sign-in-heading', _T); ?></h3>
            <h6 class="auth-subheading"><?php echo __('sign-in-heading-subtitle', _T); ?></h6>


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
                    required>
                </div>
            </div>

            <div class="form-group">
                <div class="floating-label textfield-box">
                  <label class="form-label" for="password"><?php echo __('password', _T); ?></label>
                  <input
                  type="password" name="password" id="password" class="form-control"
                  placeholder="<?php echo __("password-placeholder", _T); ?>"
                  minlength="<?php echo e_attr(config('internal.password_minlength')); ?>" required>
              </div>
          </div>


          <div class="form-group"><a href="<?php echo e_attr(url_for('auth.forgotpass')); ?>" class="sp-link float-right auth-link"><?php echo __("forgot-password", _T); ?></a></div>

          <div class="form-group">
            <div class="custom-control custom-checkbox">
              <input type="checkbox" name="remember_me" class="custom-control-input" id="remember_me" value="1" <?php checked((int) sp_post('remember_me'), 1); ?>>
              <label class="custom-control-label" for="remember_me">
                <?php echo __("remember-me", _T); ?></label>
            </div>
        </div>


        <?php echo sp_google_recaptcha('auth.signin', '<div class="recaptcha-container">', '</div>', false, [], 'auth-captcha');?>

        <div class="form-group text-right">
            <button type="submit" class="btn btn-primary">
                <span class="btn-text"><?php echo __("sign-in", _T); ?></span>
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            </button>
        </div>



        <?php if (config('site.registration_enabled')) :?>
           <p class="d-sm-none py-3"><?php echo __("sign-up-pitch", _T); ?>
            <a href="<?php echo e_attr(url_for('auth.register') . $t['redirect_to_query']);  ?>" class="sp-link"><?php echo __("sign-up-pitch-action", _T); ?></a></p>
        <?php endif; ?>
</div>
</div>
</form>
<!-- ./card -->
</div>

<div class="col-sm-7 auth-sidebar-right">

    <?php if (config('site.registration_enabled')) :?>
       <h3 class="auth-heading"><?php echo __("sign-up-pitch", _T); ?></h3>
       <h6 class="auth-subheading"><?php echo __("sign-up-pitch-subtitle", _T); ?></h6>
       <a href="<?php echo e_attr(url_for('auth.register') . $t['redirect_to_query']);  ?>" class="sp-link btn btn-outline-dark btn-lg"><?php echo __("sign-up-pitch-action", _T); ?></a>
    <?php endif; ?>

</div>
</div>
<!-- ./container -->
