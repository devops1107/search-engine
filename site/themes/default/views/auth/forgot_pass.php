<?php
/**
 * Forgot password page template
 *
 * This file contains the request page for forgot password
 */
defined('SPARKIN') or die('xD');
?>

<div class="row no-gutters auth-wrap">
    <div class="auth-sidebar-left col-sm-5 d-flex justify-content-center flex-column h-100">
        <div class="container auth-form-container">
            <form
            class="card bg-transparent shadow-none"
            method="POST"
            action="<?= e_attr(url_for('auth.forgotpass_post')); ?><?php echo $t['redirect_to_query']; ?>"
            data-recaptcha-id="auth-captcha"
            data-ajax-form="true"
            data-reset="true"
            data-parsley-validate>
            <?php echo $t['csrf_html'] . $t['honeypot_html']; ?>

            <div class="card-body">
             <div class="auth-logo-wrap">
                <a href="<?php echo e_attr(url_for('site.home')); ?>" class="sp-link auth-logo-link">
                    <img src="<?php echo e_attr($t['auth_logo_url']); ?>" class="auth-logo" alt="<?php echo e_attr(get_option('site_name')); ?>">
                </a>
            </div>

            <h3 class="auth-heading"><?php echo __('forgot-password-heading', _T); ?></h3>
            <p class="auth-help"><?php echo __('forgot-password-help', _T); ?></p>


            <?php echo sp_alert_flashes('account', true, false); ?>

            <div class="form-group">
                <div class="floating-label textfield-box">
                    <label class="form-label" for="email"><?php echo __('email', _T); ?></label>
                    <input
                    type="email" class="form-control" id="email" name="email"
                    value="<?php echo is_logged() ? e_attr(current_user_field('email')) : sp_post('email'); ?>"
                    placeholder="<?php echo e_attr(__('email-placeholder', _T)); ?>"
                    <?php echo is_logged() ? 'readonly' : ''; ?>
                    required>
                </div>
            </div>



        <?php echo sp_google_recaptcha('auth.forgotpass', '<div class="recaptcha-container">', '</div>', false, [], 'auth-captcha');?>

        <div class="form-group text-right">
            <button type="submit" class="btn btn-primary">
                <span class="btn-text"><?php echo __("reset-password", _T); ?></span>
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            </button>
        </div>

        <p class="d-sm-none py-3">
            <?php if (is_logged()) : ?>
                <?php echo __("done-heading", _T); ?> <?php echo __("go-back-to", _T); ?>
                <a href="<?php echo e_attr(url_for('site.home')); ?>" class="sp-link"><?php echo __("homepage", _T); ?></a>
            <?php else : ?>
                <?php echo __("already-have-an-account", _T); ?>
                <a href="<?php echo e_attr(url_for('auth.signin') . $t['redirect_to_query']);  ?>" class="sp-link"><?php echo __("sign-in-now", _T); ?></a>
            <?php endif; ?>
        </p>
</div>
</div>
</form>
<!-- ./card -->
</div>

<div class="col-sm-7 auth-sidebar-right">
    <?php if (is_logged()) : ?>
       <h3 class="auth-heading"><?php echo __("done-heading", _T); ?></h3>
       <h6 class="auth-subheading"><?php echo __("go-back-to", _T); ?></h6>
       <a href="<?php echo e_attr(url_for('site.home')); ?>" class="sp-link btn btn-outline-dark btn-lg"><?php echo __("homepage", _T); ?></a>
    <?php else : ?>
     <h3 class="auth-heading"><?php echo __("already-have-an-account", _T); ?></h3>
     <h6 class="auth-subheading"><?php echo __("already-have-an-account-subtitle", _T); ?></h6>
     <a href="<?php echo e_attr(url_for('auth.signin') . $t['redirect_to_query']);  ?>" class="sp-link btn btn-outline-dark btn-lg"><?php echo __("sign-in-now", _T); ?></a>
    <?php endif; ?>
</div>
</div>
<!-- ./container -->
